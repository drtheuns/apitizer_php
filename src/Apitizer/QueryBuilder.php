<?php

namespace Apitizer;

use Apitizer\DataSources\EloquentAdapter;
use Apitizer\Types\Field;
use Apitizer\Types\Association;
use Apitizer\Types\FetchSpec;
use Apitizer\Types\RequestInput;
use Apitizer\Types\Filter;
use Apitizer\Types\Apidoc;
use Illuminate\Http\Request;
use ArrayAccess;

abstract class QueryBuilder
{
    use Concerns\HasFields;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var RequestParser
     */
    protected $parser;

    /**
     * @var QueryableDataSource
     */
    protected $queryable;

    /**
     * The result of the fields() callback.
     *
     * @var Field[]|Association[]
     */
    protected $availableFields;

    /**
     * The results of the sorts() function.
     *
     * @var Sort[]
     */
    protected $availableSorts;

    /**
     * The results of the filters() function.
     *
     * @var Filter[]
     */
    protected $availableFilters;

    /**
     * The parent query builder instance.
     *
     * This is used to prevent infinite loops when dealing with associations and
     * reducing the number of operations a query builder performs, such as
     * parsing the request.
     *
     * @var QueryBuilder
     */
    protected $parent;

    /**
     * A function that returns the fields that are available to the client.
     *
     * If the value is a string, it will implicitly cast to the `AnyType`.
     *
     * Each type specifies a key that is used to fetch the data from the
     * eventual source data. In other words, if the query builder is used in
     * conjunction with a database and Eloquent, then the key would be the key
     * on the Eloquent model that should be used to fetch the data (usually the
     * column name in the database).
     */
    abstract public function fields(): array;

    /**
     * A function that returns the names of the sorting methods that are
     * available to the client.
     *
     * The following sorts:
     *
     *   ['name' => ColumnSort()]
     *
     * would support the following queries:
     *
     *   /users?sort=name.asc
     */
    abstract public function sorts(): array;

    /**
     * A function that returns the filters that are available to the client.
     */
    abstract public function filters(): array;

    /**
     * Get the source that can be used by the queryable.
     *
     * In the case of Eloquent, this function should return a query or model
     * object, whereas for a different adapter you will want to return a
     * different source.
     */
    abstract public function model();

    /**
     * Overridable function to adjust the API documentation for this query
     * builder.
     *
     * @see Apidoc
     */
    public function apidoc(Apidoc $apidoc)
    {
        //
    }

    public function __construct(
        Request $request,
        QueryableDataSource $queryable = null,
        RequestParser $parser = null
    ) {
        $this->request = $request;
        $this->queryable = $queryable ?? new EloquentAdapter();
        $this->parser = $parser ?? new RequestParser();
    }

    /**
     * Static factory method; essentially alias for the constructor.
     */
    public static function make(
        Request $request,
        QueryableDataSource $queryable = null,
        RequestParser $parser = null
    ) {
        return (new static($request, $queryable, $parser));
    }

    /**
     * Defines a relationship to another querybuilder.
     *
     * This can be used in the `fields` callback to handle nested selects such
     * as:
     *
     *   /users?select=id,comments(id,body)
     *
     * where `comments` is defined like:
     *
     *   $this->association('comments', CommentBuilder::class)
     *
     * @param string $key
     * @param string|QueryBuilder $builder
     *
     * @return Association
     */
    protected function association(string $key, string $builder)
    {
        $builderInstance = $this->getParentByClassName($builder);

        if (! $builderInstance) {
            $builderInstance = new $builder($this->request, $this->queryable, $this->parser);
            $builderInstance->setParent($this);
        }

        return new Association($key, $builderInstance);
    }

    /**
     * Start building a new filter.
     *
     * @return Filter
     */
    protected function filter(): Filter
    {
        return new Filter($this);
    }

    /**
     * Render the given data based on the current query builder and request.
     *
     * @return array
     */
    public function render($data): array
    {
        $fetchSpec = $this->makeFetchSpecification();

        return $this->transformValues($data, $fetchSpec->getFields());
    }

    /**
     * Fetch and render all the data.
     *
     * @return array
     */
    public function all(): array
    {
        $fetchSpec = $this->makeFetchSpecification();

        return $this->transformValues(
            $this->queryable->fetchData($this, $fetchSpec),
            $fetchSpec->getFields()
        );
    }

    /**
     * Fetch and return paginated data.
     */
    public function paginate()
    {
        $fetchSpec = $this->makeFetchSpecification();

        return $this->transformValues(
            // TODO: Implement pagination
            $this->queryable->fetchData($this, $fetchSpec),
            $fetchSpec->getFields()
        );
    }

    /**
     * Build the fetch specification based on the query builder and the request.
     *
     * @return FetchSpec
     */
    protected function makeFetchSpecification(): FetchSpec
    {
        $fetchSpec = $this->validateRequestInput(
            $this->parser->parse($this->request)
        );

        if (empty($fetchSpec->getFields())) {
            // If nothing (valid) is selected, return all non-association fields.
            $fetchSpec->setFields(
                array_filter($this->getFields(), function ($field) {
                    return $field instanceof Field;
                })
            );
        }

        return $fetchSpec;
    }

    /**
     * Validate that each field has a correct type, possibly assigning the any
     * type.
     *
     * @return Map<string, Field|Association>
     */
    protected function castFields(array $fields): array
    {
        $castFields = [];

        foreach ($fields as $name => $field) {
            if ($field instanceof Association) {
                $field->setName($name);
                $castFields[$name] = $field;
                continue;
            }

            if (is_string($field)) {
                $field = $this->any($field);
            }

            if (! $field instanceof Field) {
                throw new \UnexpectedValueException(
                    "Unexpected field type for {$name}: {gettype($field)}"
                );
            }

            $field->setName($name);
            $castFields[$name] = $field;
        }

        return $castFields;
    }

    protected function castFilters(array $filters): array
    {
        foreach ($filters as $name => $filter) {
            if (! $filter instanceof Filter) {
                throw new \UnexpectedValueException(
                    "expected Filter to be returned for {$name}"
                );
            }

            $filter->setName($name);
        }

        return $filters;
    }

    protected function validateRequestInput(RequestInput $unvalidatedInput): FetchSpec
    {
        $validated = new FetchSpec(
            $this->validateFields($unvalidatedInput->fields, $this->getFields()),
            $this->validateSorting($unvalidatedInput->sorts, $this->getSorts()),
            $this->validateFilters($unvalidatedInput->filters, $this->getFilters()),
        );

        return $validated;
    }

    /**
     * Validate the fields that were requested by the client.
     *
     * @return Map<string, Field|Association>
     */
    protected function validateFields($unvalidatedFields, $availableFields): array
    {
        $validatedFields = [];

        // Essentially get a subset of $availableFields with some type juggling
        // along the way.
        foreach ($unvalidatedFields as $field) {
            if ($field instanceof Parser\Relation && isset($availableFields[$field->name])) {
                // Convert the Parser\Relation to an Association object.
                $association = $availableFields[$field->name];

                // Validate the fields recursively
                $association->setFields(
                    $this->validateFields($field->fields, $association->getQueryBuilder()->getFields())
                );
                $validatedFields[] = $association;

                continue;
            }

            if (is_string($field) && isset($availableFields[$field])) {
                $validatedFields[] = $availableFields[$field];
            }
        }

        return $validatedFields;
    }

    protected function validateSorting(array $selectedSorts, array $availableSorts): array
    {
        $validatedSorts = [];

        foreach ($selectedSorts as $sort) {
            if (isset($availableSorts[$sort->getField()])) {
                $sort->setHandler($availableSorts[$sort->getField()]);
                $validatedSorts[] = $sort;
            }
        }

        return $validatedSorts;
    }

    protected function validateFilters(array $selectedFilters, array $availableFilters): array
    {
        $validatedFilters = [];

        foreach ($selectedFilters as $name => $filterInput) {
            try {
                if (is_string($name) && isset($availableFilters[$name])) {
                    $filter = $availableFilters[$name];
                    $filter->setValue($filter->validateInput($filterInput));
                    $validatedFilters[$name] = $filter;
                }
            } catch (\UnexpectedValueException $e) {
                // Ignore this filter.
            }
        }

        return $validatedFilters;
    }

    public function transformValues($data, array $selectedFields): array
    {
        // Check if we're dealing with a single row of data.
        if ($this->isSingleDataModel($data) || $this->isNonCollectionObject($data)) {
            return $this->transformOne($data, $selectedFields);
        }

        $result = [];

        foreach ($data as $row) {
            $result[] = $this->transformOne($row, $selectedFields);
        }

        return $result;
    }

    protected function transformOne(ArrayAccess $row, array $selectedFields): array
    {
        $acc = [];

        foreach ($selectedFields as $fieldOrAssoc) {
            $acc[$fieldOrAssoc->getName()] = $fieldOrAssoc->render($row);
        }

        return $acc;
    }

    protected function isSingleDataModel($data): bool
    {
        // Distinguish between arrays as list and arrays as maps.
        return is_array($data) && $this->isAssoc($data);
    }

    protected function isNonCollectionObject($data): bool
    {
        // Distinguish between e.g. Eloquent objects and Collection objects.
        return is_object($data) && ! is_iterable($data);
    }

    private function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    public function getFields(): array
    {
        if (is_null($this->availableFields)) {
            $this->availableFields = $this->castFields($this->fields());
        }

        return $this->availableFields;
    }

    public function getSorts(): array
    {
        if (is_null($this->availableSorts)) {
            $this->availableSorts = $this->sorts();
        }

        return $this->availableSorts;
    }

    public function getFilters(): array
    {
        if (is_null($this->availableFilters)) {
            $this->availableFilters = $this->castFilters($this->filters());
        }

        return $this->availableFilters;
    }

    public function setParent(QueryBuilder $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent(): ?QueryBuilder
    {
        return $this->parent;
    }

    /**
     * Traverse the parents to find out if any of them are of the same instance
     * as the given class name.
     *
     * @return null|QueryBuilder
     */
    public function getParentByClassName(string $className): ?QueryBuilder
    {
        $parent = $this;

        while (! is_null($parent)) {
            $parent = $parent->getParent();

            if ($parent && \get_class($parent) === $className) {
                return $parent;
            }
        }

        return null;
    }
}
