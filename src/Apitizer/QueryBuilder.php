<?php

namespace Apitizer;

use Apitizer\DataSources\EloquentAdapter;
use Apitizer\Types\Field;
use Apitizer\Types\Association;
use Apitizer\Types\FetchSpec;
use Apitizer\Types\RequestInput;
use Illuminate\Http\Request;

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

    // These variables contain the results of the fields(), sorts(), and
    // filters() method.
    /** @var Map<string, Field|Association> */
    protected $availableFields;
    protected $availableSorts;
    protected $availableFilters;

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
     *   ['name']
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
    abstract public function datasource();

    /**
     * Overridable function to adjust the API documentation for this query
     * builder.
     *
     * @see Apidoc
     */
    protected function apidoc(Apidoc $apidoc)
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

        // Eagerly prepare the builder for later.
        $this->availableFields = $this->castFields($this->fields());
        $this->availableSorts = $this->sorts();
        $this->availableFilters = $this->filters();
    }

    /**
     * Validate that each field has a correct type, possibly assigning the any
     * type.
     *
     * @return Map<string, Field|Association>
     */
    protected function castFields(array $fields)
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
     */
    protected function association(string $key, string $builder)
    {
        return new Association(
            $key,
            new $builder($this->request, $this->queryable, $this->parser)
        );
    }

    public function build()
    {
        $unvalidatedInput = $this->parser->parse($this->request);

        $fetchSpec = $this->validateRequestInput($unvalidatedInput);

        if (empty($fetchSpec->getFields())) {
            // If nothing (valid) is selected, return all non-association fields.
            $fetchSpec->setFields(
                array_filter($this->getFields(), function ($field) {
                    return $field instanceof Field;
                })
            );
        }

        $data = $this->queryable->fetchData($this, $fetchSpec);

        return $this->transformValues($data, $fetchSpec->getFields());
    }

    protected function validateRequestInput(RequestInput $unvalidatedInput): FetchSpec
    {
        $validated = new FetchSpec(
            $this->validateFields($unvalidatedInput->fields, $this->getFields()),
            $this->validateSorting($unvalidatedInput->sorts, $this->getSorts()),
            []
        );

        return $validated;
    }

    /**
     * Validate the fields that were requested by the client.
     *
     * @return Map<string, Field|Association>
     */
    public function validateFields($unvalidatedFields, $availableFields)
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
                    $this->validateFields($field->fields, $association->getBuilder()->getFields())
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

    public function validateSorting(array $selectedSorts, array $availableSorts)
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

    public function transformValues(iterable $data, array $selectedFields)
    {
        // Check if we're dealing with a single row of data.
        if (is_array($data) && $this->isAssoc($data)) {
            return $this->transformOne($data, $selectedFields);
        }

        $result = [];

        foreach ($data as $row) {
            $result[] = $this->transformOne($row, $selectedFields);
        }

        return $result;
    }

    protected function transformOne($row, $selectedFields)
    {
        $acc = [];

        foreach ($selectedFields as $fieldOrAssoc) {
            $acc[$fieldOrAssoc->getName()] = $fieldOrAssoc->render($row);
        }

        return $acc;
    }

    private function isAssoc(array $array)
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    public function getFields()
    {
        return $this->availableFields;
    }

    public function getSorts()
    {
        return $this->availableSorts;
    }

    public function getFilters()
    {
        return $this->availableFilters;
    }
}
