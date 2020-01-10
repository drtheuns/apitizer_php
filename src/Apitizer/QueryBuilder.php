<?php

namespace Apitizer;

use Apitizer\Exceptions\ApitizerException;
use Apitizer\Exceptions\DefinitionException;
use Apitizer\Exceptions\InvalidInputException;
use Apitizer\ExceptionStrategy\Strategy;
use Apitizer\Interpreter\QueryInterpreter;
use Apitizer\Parser\InputParser;
use Apitizer\Parser\ParsedInput;
use Apitizer\Parser\Parser;
use Apitizer\Parser\RawInput;
use Apitizer\Parser\Relation;
use Apitizer\Rendering\Renderer;
use Apitizer\Types\Apidoc;
use Apitizer\Types\Association;
use Apitizer\Types\FetchSpec;
use Apitizer\Types\Field;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

abstract class QueryBuilder
{
    use Concerns\HasFields;

    /**
     * @var null|Request
     */
    protected $request;

    /**
     * @var InputParser
     */
    protected $parser;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var QueryInterpreter
     */
    protected $queryInterpreter;

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
     * The maximum number of rows that the client is able to request.
     *
     * @param int
     */
    protected $maximumLimit = 50;

    /**
     * The strategy to use when an exception is raised.
     *
     * @var Strategy
     */
    protected $exceptionStrategy;

    /**
     * A function that returns the fields that are available to the client.
     *
     * If the value is a string, it will be implicitly cast to `$this->any`
     *
     * Each type (e.g. `$this->string`) expects at least a key string. This key
     * is used to fetch the data from the Eloquent model, so it usually
     * corresponds to the column name in the database.
     */
    abstract public function fields(): array;

    /**
     * A function that returns the names of the sorting methods that are
     * available to the client.
     *
     * The following sorts:
     *
     *   ['name' => $this->sort()->byField('name')]
     *
     * would support the following queries:
     *
     *   /users?sort=name.asc
     *
     * @see $this->sort()
     * @see \Apitizer\Types\Sort
     */
    abstract public function sorts(): array;

    /**
     * A function that returns the filters that are available to the client.
     *
     * The key of the array is the name of the filter that is displayed to the
     * client, while the value must be a Filter object.
     *
     * @see $this->filter()
     * @see \Apitizer\Types\Filter
     */
    abstract public function filters(): array;

    /**
     * Get the source that will be used by the query interpreter.
     */
    abstract public function model(): Model;

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

    public function __construct(Request $request = null) {
        $this->setRequest($request);
    }

    /**
     * Create a new instance with a request object.
     *
     * If you need to pass other variables, such as a custom QueryInterpreter,
     * use the constructor instead.
     */
    public static function make(Request $request)
    {
        return (new static($request));
    }

    /**
     * Build a query object using this builder without actually fetching the data.
     *
     * @return Builder
     */
    public static function build(Request $request = null): Builder
    {
        return (new static($request))->buildQuery();
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
     * @param string $builder
     *
     * @return Association
     */
    protected function association(string $key, string $builderClass)
    {
        $builderInstance = $this->getParentByClassName($builderClass);

        if (! $builderInstance) {
            $builderInstance = $this->createChildBuilder($builderClass);
        }

        if (! $this->isValidAssociation($key)) {
            throw DefinitionException::associationDoesNotExist($key);
        }

        return new Association($builderInstance, $key);
    }

    protected function createChildBuilder(string $builderClass)
    {
        $builder = new $builderClass();

        if (! $builder instanceof QueryBuilder) {
            throw DefinitionException::builderClassExpected($builderClass);
        }

        // setParent will take care of all the other setters.
        $builder->setParent($this);

        return $builder;
    }

    protected function isValidAssociation(string $associationName)
    {
        return method_exists($this->model(), $associationName)
            && $this->model()->{$associationName}() instanceof EloquentRelation;
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
     * Start building a new sorting handler.
     *
     * @return Sort
     */
    protected function sort(): Sort
    {
        return new Sort($this);
    }

    /**
     * Render the given data based on either the current query builder and
     * request, or the fields that were passed in.
     *
     * @param mixed $data
     * @param (Field|Association)[] $fields
     *
     * @return array
     */
    public function render($data, array $fields = null): array
    {
        $fieldsToRender = $fields ?? $this->makeFetchSpecification()->getFields();
        // TODO: Validate & convert the fields that were passed in.

        return $this->getRenderer()->render($this, $data, $fieldsToRender);
    }

    /**
     * Fetch and render all the data.
     *
     * @return array
     */
    public function all(): array
    {
        $fetchSpec = $this->makeFetchSpecification();

        return $this->getRenderer()->render(
            $this,
            $this->getQueryInterpreter()->build($this, $fetchSpec)->get(),
            $fetchSpec->getFields()
        );
    }

    /**
     * Fetch and return paginated data.
     *
     * 
     *
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = null, ...$rest): LengthAwarePaginator
    {
        $fetchSpec = $this->makeFetchSpecification();
        $perPage = $this->getPerPage($perPage);
        $paginator = $this->getQueryInterpreter()
                          ->build($this, $fetchSpec)
                          ->paginate($perPage, ...$rest);

        return tap($paginator, function (AbstractPaginator $paginator) use ($fetchSpec) {
            $renderedData = $this->getRenderer()->render(
                $this, $paginator->getCollection(), $fetchSpec->getFields()
            );

            $paginator->setCollection(collect($renderedData));

            // Ensure the all the supported query parameters that were passed in are
            // also present in the pagination links.
            $queryParameters = Arr::only($this->getRequest()->query(), Apitizer::getQueryParams());
            $paginator->appends($queryParameters);
        });
    }

    protected function getPerPage(int $perPage = null)
    {
        $limitKey = Apitizer::getLimitKey();

        if ($this->getRequest()->has($limitKey)) {
            // The limit must be in range(1, $this->maximumLimit)
            $perPage = $this->getRequest()->input($limitKey);
        }

        if (isset($perPage)) {
            $perPage = max(1, min($this->getRequest()->input($limitKey), $this->getMaximumLimit()));
        }

        return $perPage;
    }

    /**
     * Build the query without actually fetching the data.
     *
     * @return Builder
     */
    public function buildQuery(): Builder
    {
        return $this->getQueryInterpreter()->build(
            $this, $this->makeFetchSpecification()
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
            $this->getParser()->parse(RawInput::fromRequest($this->getRequest()))
        );

        if (empty($fetchSpec->getFields())) {
            // If nothing (valid) is selected, return all non-association fields.
            $fetchSpec->setFields($this->getOnlyFields());
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
                throw DefinitionException::fieldDefinitionExpected($name, $field);
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
                throw DefinitionException::filterDefinitionExpected($name, $filter);
            }

            $filter->setName($name);
        }

        return $filters;
    }

    protected function castSorts(array $sorts): array
    {
        foreach ($sorts as $name => $sort) {
            if (! $sort instanceof Sort) {
                throw DefinitionException::sortDefinitionExpected($name, $sort);
            }
            $sort->setName($name);
        }

        return $sorts;
    }

    protected function validateRequestInput(ParsedInput $unvalidatedInput): FetchSpec
    {
        $validated = new FetchSpec(
            $this->getValidatedFields($unvalidatedInput->fields, $this->getFields()),
            $this->getValidatedSorting($unvalidatedInput->sorts, $this->getSorts()),
            $this->getValidatedFilters($unvalidatedInput->filters, $this->getFilters()),
        );

        return $validated;
    }

    /**
     * Validate the fields that were requested by the client.
     *
     * @return [string => Field|Association]
     */
    protected function getValidatedFields(array $unvalidatedFields, array $availableFields): array
    {
        $validatedFields = [];

        // Essentially get a subset of $availableFields with some type juggling
        // along the way.
        foreach ($unvalidatedFields as $field) {
            if ($field instanceof Relation && isset($availableFields[$field->name])) {
                // Convert the Parser\Relation to an Association object.
                $association = $availableFields[$field->name];

                // Validate the fields recursively
                $association->setFields(
                    $this->getValidatedFields($field->fields, $association->getQueryBuilder()->getFields())
                );
                $validatedFields[] = $association;

                continue;
            }

            if (is_string($field) && isset($availableFields[$field])) {
                $fieldInstance = $availableFields[$field];

                // An association was selected without specifying any fields, e.g.:
                // /posts?fields=comments
                // .. so we select everything from that query builder.
                if ($fieldInstance instanceof Association) {
                    $fieldInstance->setFields($fieldInstance->getQueryBuilder()->getOnlyFields());
                }

                $validatedFields[] = $availableFields[$field];
            }
        }

        return $validatedFields;
    }

    protected function getValidatedSorting(array $selectedSorts, array $availableSorts): array
    {
        $validatedSorts = [];

        foreach ($selectedSorts as $parserSort) {
            if (isset($availableSorts[$parserSort->getField()])) {
                $sort = $availableSorts[$parserSort->getField()];
                $sort->setOrder($parserSort->getOrder());
                $validatedSorts[] = $sort;
            }
        }

        return $validatedSorts;
    }

    protected function getValidatedFilters(array $selectedFilters, array $availableFilters): array
    {
        $validatedFilters = [];

        foreach ($selectedFilters as $name => $filterInput) {
            try {
                if (is_string($name) && isset($availableFilters[$name])) {
                    $filter = $availableFilters[$name];
                    $filter->setValue($filterInput);
                    $validatedFilters[$name] = $filter;
                }
            } catch (InvalidInputException $e) {
                $this->getExceptionStrategy()->handle($this, $e);
            }
        }

        return $validatedFilters;
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
            $this->availableSorts = $this->castSorts($this->sorts());
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

    /**
     * @return Field[]
     */
    public function getOnlyFields(): array
    {
        return array_filter($this->getFields(), function ($field) {
            return $field instanceof Field;
        });
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

    public function getRequest(): Request
    {
        if (! $this->request) {
            $this->request = request();
        }

        return $this->request;
    }

    public function setRequest(?Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getRenderer(): Renderer
    {
        if (! $this->renderer) {
            $this->renderer = app(Renderer::class);
        }

        return $this->renderer;
    }

    public function setRenderer(Renderer $renderer): self
    {
        $this->renderer = $renderer;

        return $this;
    }

    public function getQueryInterpreter(): QueryInterpreter
    {
        if (! $this->queryInterpreter) {
            $this->queryInterpreter = new QueryInterpreter();
        }

        return $this->queryInterpreter;
    }

    public function setQueryInterpreter(QueryInterpreter $queryInterpreter): self
    {
        $this->queryInterpreter = $queryInterpreter;

        return $this;
    }

    public function getParser(): Parser
    {
        if (! $this->parser) {
            $this->parser = app(Parser::class);
        }

        return $this->parser;
    }

    public function setParser(Parser $requestParser): self
    {
        $this->parser = $requestParser;

        return $this;
    }

    public function getExceptionStrategy(): Strategy
    {
        if (! $this->exceptionStrategy) {
            $this->exceptionStrategy = app(Strategy::class);
        }

        return $this->exceptionStrategy;
    }

    public function setExceptionStrategy(Strategy $strategy): self
    {
        $this->exceptionStrategy = $strategy;

        return $this;
    }

    public function handleException(ApitizerException $e)
    {
        return $this->getExceptionStrategy()->handle($this, $e);
    }

    public function getParent(): ?QueryBuilder
    {
        return $this->parent;
    }

    public function setParent(QueryBuilder $parent): self
    {
        $this->parent = $parent;
        $this->setRequest($parent->getRequest())
             ->setQueryInterpreter($parent->getQueryInterpreter())
             ->setParser($parent->getParser())
             ->setRenderer($parent->getRenderer());

        return $this;
    }

    public function getMaximumLimit(): int
    {
        return $this->maximumLimit;
    }

    public function setMaximumLimit(int $limit): self
    {
        $this->maximumLimit = $limit;

        return $this;
    }
}
