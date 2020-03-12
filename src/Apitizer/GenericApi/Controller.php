<?php

namespace Apitizer\GenericApi;

use Apitizer\Interpreter\GenericApiQueryInterpreter;
use Apitizer\Schema;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * The generic controller is an ease-of-use feature so people don't have to
 * write the same controller for each resource.
 */
class Controller extends BaseController
{
    /**
     * @var Schema the schema that is responsible for handling the
     * requests.
     */
    protected $schema;

    /**
     * @var array{schema: class-string<Schema>,
     *            service: string|null,
     *            service_method: string|null,
     *            routeParameters: array<string, array{schema: class-string,
     *                                                 has_param: bool,
     *                                                 association: string|null}>}
     */
    protected $metadata;

    /**
     * @var RouteParameter[]
     */
    protected $routeParameters;

    /**
     * @param string $method
     * @param array<mixed> $parameters
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        $this->setup();

        return parent::callAction($method, $parameters);
    }

    protected function setup(): void
    {
        $request = request();

        /** @var \Illuminate\Routing\Route $route */
        $route = $request->route();

        /**
         * @var array{schema: class-string<Schema>,
         *            service: string|null,
         *            service_method: string|null,
         *            routeParameters: array<string, array{schema: class-string,
         *                                                 has_param:bool,
         *                                                 association: string|null}>} $metadata
         * @see \Apitizer\Routing\SchemaRoute::metadata
         */
        $metadata = $route->action['metadata'];

        $schema = $metadata['schema'];
        $this->schema = new $schema($request);
        $this->metadata = $metadata;

        $this->routeParameters = $this->prepareRouteParameters($request);
    }

    /**
     * @return array<string, mixed>|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return $this->schema
            ->setQueryInterpreter(new GenericApiQueryInterpreter($this->routeParameters))
            ->paginate();
    }

    /**
     * @return array<string, mixed>
     */
    public function show(Request $request)
    {
        $model = $this->schema
               ->setQueryInterpreter(new GenericApiQueryInterpreter($this->routeParameters))
               ->buildQuery()
               ->first();

        /** @var array<string, mixed> $rendered */
        $rendered = $this->schema->render($model);

        return $rendered;
    }

    /**
     * @return RouteParameter[]
     */
    protected function prepareRouteParameters(Request $request): array
    {
        $routeParameters = [];

        foreach ($this->metadata['routeParameters'] as $param => $paramInfo) {
            $routeParameter = RouteParameter::fromRouteMetadata($param, $paramInfo);
            $routeParameter->setValue($request->route($param, null));
            $routeParameters[] = $routeParameter;
        }

        return $routeParameters;
    }
}
