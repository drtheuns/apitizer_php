<?php

namespace Apitizer;

use Apitizer\Exceptions\ApitizerException;
use Apitizer\Exceptions\DefinitionException;
use Apitizer\ExceptionStrategy\Strategy;
use Apitizer\Interpreter\QueryInterpreter;
use Apitizer\Parser\Parser;
use Apitizer\Parser\RawInput;
use Apitizer\Rendering\Renderer;
use Apitizer\Support\DefinitionHelper;
use Apitizer\Support\FetchSpecFactory;
use Apitizer\Types\Apidoc;
use Apitizer\Types\Association;
use Apitizer\Types\FetchSpec;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;
use Apitizer\Validation\Rules;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
     * Columns that should always be loaded, even if they were not explicitly
     * requested. These should be the columns on the Eloquent model, not the
     * field name. Although these columns will be loaded, they will not be added
     * to the response output.
     *
     * This can be especially helpful when you have policies or other checks
     * that depend on certain data being present.
     *
     * @var string[]
     */
    protected $alwaysLoadColumns = [];

    /**
     * @var Parser|null
     */
    protected $parser;

    /**
     * @var Renderer|null
     */
    protected $renderer;

    /**
     * @var QueryInterpreter|null
     */
    protected $queryInterpreter;

    /**
     * The result of the fields() callback.
     *
     * @var AbstractField[]|null
     */
    protected $availableFields;

    /**
     * The result of the associations() callback
     *
     * @var Association[]|null
     */
    protected $availableAssociations;

    /**
     * The results of the sorts() function.
     *
     * @var array<string, Sort>|null
     */
    protected $availableSorts;

    /**
     * The results of the filters() function.
     *
     * @var array<string, Filter>|null
     */
    protected $availableFilters;

    /**
     * @var array{fields: string|string[], sorts: string|string[], filters: array<string, mixed>}|null
     * the specification that should be used when fetching or rendering data.
     * This is an alternative to the input from the Request object; therefore,
     * it this is null, the request's input will be used.
     */
    protected $specification;

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
     * @var int
     */
    protected $maximumLimit = 50;

    /**
     * The strategy to use when an exception is raised.
     *
     * @var Strategy|null
     */
    protected $exceptionStrategy;

    /**
     * @var Rules|null
     */
    protected $rules;

    /**
     * A function that returns the fields that are available to the client.
     *
     * If the value is a string, it will be implicitly cast to `$this->any`
     *
     * Each type (e.g. `$this->string`) expects at least a key string. This key
     * is used to fetch the data from the Eloquent model, so it usually
     * corresponds to the column name in the database.
     *
     * @return array<string, AbstractField>
     */
    abstract public function fields(): array;

    /**
     * A callback that returns all the associations that are available to the
     * client.
     *
     * @see QueryBuilder::association
     *
     * @return array<string, Association>
     */
    abstract public function associations(): array;

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
     *
     * @return array<string, Sort>
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
     *
     * @return array<string, Filter>
     */
    abstract public function filters(): array;

    /**
     * Get the source that will be used by the query interpreter.
     */
    abstract public function model(): Model;

    /**
     * Build the validation rules for the various route actions.
     *
     * @param Rules $rules
     *
     * @return void
     */
    abstract public function rules(Rules $rules);

    /**
     * Overridable function to adjust the API documentation for this query
     * builder.
     *
     * @see Apidoc
     */
    public function apidoc(Apidoc $apidoc): void
    {
        //
    }

    /**
     * This function is called before the query is built and conditions are applied.
     * It allows query builders to hook into the query building process and
     * modify the query based on state and user input.
     *
     * For example, if you need to always add conditions to the query based on
     * the user's role, this would be the place to put it.
     *
     * @see QueryBuilder::getRequest()
     * @see FetchSpec::fieldSelected
     * @see FetchSpec::filterSelected
     * @see FetchSpec::sortSelected
     */
    public function beforeQuery(Builder $query, FetchSpec $fetchSpec): Builder
    {
        return $query;
    }

    /**
     * Same as the beforeQuery method, this function can be used to hook into
     * the query building process when it is done (but the data hasn't been
     * fetched yet).
     *
     * @see QueryBuilder::beforeQuery
     */
    public function afterQuery(Builder $query, FetchSpec $fetchSpec): Builder
    {
        return $query;
    }

    public function __construct(Request $request = null) {
        $this->setRequest($request);
    }

    /**
     * Static alias for the constructor.
     */
    public static function make(Request $request = null): QueryBuilder
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
     * @param array{fields: string|string[], sorts: string|string[], filters: array<string, mixed>} $specification
     * array the specification of data that should be used for this builder.
     * This array may contain three keys: `fields`, `filters`, and `sorts`. The
     * value for these should be the same as what you would send in a request;
     * in other words, fields may be a comma separated string of fields (or an
     * array), etc.
     */
    public function fromSpecification(array $specification): self
    {
        $this->specification = $specification;

        return $this;
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
     * @param string $builderClass
     *
     * @return Association
     */
    protected function association(string $key, string $builderClass)
    {
        $builderInstance = $this->getParentByClassName($builderClass);

        if (! $builderInstance) {
            $builderInstance = new $builderClass();

            if (! $builderInstance instanceof QueryBuilder) {
                throw DefinitionException::builderClassExpected($this, $key, $builderClass);
            }

            // setParent will take care of all the other setters.
            $builderInstance->setParent($this);
        }

        return new Association($this, $builderInstance, $key);
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
     *
     * @return array<int, array<string, mixed>>|array<string, mixed>
     */
    public function render($data): array
    {
        $fetchSpec = $this->makeFetchSpecification();

        return $this->getRenderer()->render($this, $data, $fetchSpec);
    }

    /**
     * Fetch and render all the data.
     *
     * @return array<mixed>
     */
    public function all(): array
    {
        $fetchSpec = $this->makeFetchSpecification();

        return $this->getRenderer()->render(
            $this,
            $this->getQueryInterpreter()->build($this, $fetchSpec)->get(),
            $fetchSpec
        );
    }

    /**
     * Fetch and return paginated data.
     *
     * @param int $perPage
     * @param string  $pageName
     * @param int|null  $page
     *
     * @return LengthAwarePaginator<array>
     */
    public function paginate(int $perPage = null, $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $fetchSpec = $this->makeFetchSpecification();
        $perPage = $this->getPerPage($perPage);
        $paginator = $this->getQueryInterpreter()
                          ->build($this, $fetchSpec)
                          ->paginate($perPage, [], $pageName, $page);

        return tap($paginator, function (AbstractPaginator $paginator) use ($fetchSpec) {
            $renderedData = $this->getRenderer()->render(
                $this, $paginator->getCollection(), $fetchSpec
            );

            $paginator->setCollection(collect($renderedData));

            /** @var array<string, mixed> $queryParameters */
            $queryParameters = $this->getRequest()->query();

            // Ensure the all the supported query parameters that were passed in are
            // also present in the pagination links.
            $queryParameters = Arr::only(
                $queryParameters,
                array_values(Apitizer::getQueryParams())
            );
            $paginator->appends($queryParameters);
        });
    }

    protected function getPerPage(int $perPage = null): ?int
    {
        $limitKey = Apitizer::getLimitKey();
        $request = $this->getRequest();

        if ($request->has($limitKey)) {
            // The limit must be in range(1, $this->maximumLimit)
            $perPage = $request->input($limitKey);
        }

        if (isset($perPage)) {
            $perPage = max(1, min($request->input($limitKey), $this->getMaximumLimit()));
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
        return $this->getQueryInterpreter()
                    ->build($this, $this->makeFetchSpecification());
    }

    /**
     * Get the validation rules for the current request.
     *
     * @return array<string, string|\Illuminate\Contracts\Validation\Rule>
     */
    public function validationRules(): array
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = $this->getRequest()->route();
        $actionMethod = $route->getActionMethod();

        return $this->getRules()->getValidationRulesForAction($actionMethod);
    }

    /**
     * Get an instantiated validator object with the rules for the current
     * action method.
     */
    public function validator(): Validator
    {
        return ValidatorFactory::make($this->getRequest()->all(), $this->validationRules());
    }

    /**
     * Return the validated data for the current request, based on the request's
     * action method.
     *
     * @return array<string, mixed>
     */
    public function validated(): array
    {
        return $this->validator()->validate();
    }

    /**
     * Build the fetch specification based on the query builder and the request.
     *
     * @return FetchSpec
     */
    protected function makeFetchSpecification(): FetchSpec
    {
        $rawInput = is_null($this->specification)
                  ? RawInput::fromRequest($this->getRequest())
                  : RawInput::fromArray($this->specification);

        return FetchSpecFactory::fromRequestInput(
            $this->getParser()->parse($rawInput), $this
        );
    }

    /**
     * @return array<string, AbstractField>
     */
    public function getFields(): array
    {
        if (is_null($this->availableFields)) {
            $this->availableFields = DefinitionHelper::validateFields($this, $this->fields());
        }

        return $this->availableFields;
    }

    /**
     * @return array<string, Association>
     */
    public function getAssociations(): array
    {
        if (is_null($this->availableAssociations)) {
            $this->availableAssociations = DefinitionHelper::validateAssociations(
                $this, $this->associations()
            );
        }

        return $this->availableAssociations;
    }

    /**
     * @return array<string, Sort>
     */
    public function getSorts(): array
    {
        if (is_null($this->availableSorts)) {
            $this->availableSorts = DefinitionHelper::validateSorts($this, $this->sorts());
        }

        return $this->availableSorts;
    }

    /**
     * @return array<string, Filter>
     */
    public function getFilters(): array
    {
        if (is_null($this->availableFilters)) {
            $this->availableFilters = DefinitionHelper::validateFilters($this, $this->filters());
        }

        return $this->availableFilters;
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

    public function setRenderer(?Renderer $renderer): self
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

    public function setQueryInterpreter(?QueryInterpreter $queryInterpreter): self
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

    public function setParser(?Parser $requestParser): self
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

    public function setExceptionStrategy(?Strategy $strategy): self
    {
        $this->exceptionStrategy = $strategy;

        return $this;
    }

    public function handleException(ApitizerException $e): void
    {
        $this->getExceptionStrategy()->handle($this, $e);
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

    /**
     * @return string[]
     */
    public function getAlwaysLoadColumns(): array
    {
        return $this->alwaysLoadColumns;
    }

    public function getRules(): Rules
    {
        $rules = $this->rules;

        if (! $rules) {
            $rules = new Rules();
            $this->rules($rules);
            $this->rules = $rules;
        }

        return $rules;
    }
}
