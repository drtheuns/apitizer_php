<?php

namespace Apitizer\GenericApi;

use Apitizer\Interpreter\GenericApiQueryInterpreter;
use Apitizer\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * The generic controller is an ease-of-use feature so people don't have to
 * write the same controller for each resource.
 */
class Controller extends BaseController
{
    /**
     * @var QueryBuilder the query builder that is responsible for handling the
     * requests.
     */
    protected $queryBuilder;

    /**
     * @var array{schema: class-string<QueryBuilder>,
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
         * @var array{schema: class-string<QueryBuilder>,
         *            service: string|null,
         *            service_method: string|null,
         *            routeParameters: array<string, array{schema: class-string,
         *                                                 has_param:bool,
         *                                                 association: string|null}>} $metadata
         * @see \Apitizer\Routing\SchemaRoute::metadata
         */
        $metadata = $route->action['metadata'];

        $schema = $metadata['schema'];
        $this->queryBuilder = new $schema($request);
        $this->metadata = $metadata;

        $this->routeParameters = $this->prepareRouteParameters($request);
    }

    /**
     * @return array<string, mixed>|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return $this->queryBuilder
            ->setQueryInterpreter(new GenericApiQueryInterpreter($this->routeParameters))
            ->paginate();
    }

    /**
     * @return array<string, mixed>
     */
    public function show(Request $request)
    {
        $model = $this->queryBuilder
               ->setQueryInterpreter(new GenericApiQueryInterpreter($this->routeParameters))
               ->buildQuery()
               ->first();

        /** @var array<string, mixed> $rendered */
        $rendered = $this->queryBuilder->render($model);

        return $rendered;
    }

    // /**
    //  * @return array<string, mixed>
    //  */
    // public function store(Request $request)
    // {
    //     $model = $this->service->create(
    //         $this->queryBuilder->validated(),
    //         $this->queryBuilder
    //     );

    //     /** @var array<string, mixed> $rendered */
    //     $rendered = $this->queryBuilder->render($model);

    //     return $rendered;
    // }

    // /**
    //  * @return array<string, mixed>
    //  */
    // public function update(Request $request)
    // {
    //     $model = $this->service->update(
    //         $this->getModelFromRequest($request),
    //         $this->queryBuilder->validated(),
    //         $this->queryBuilder
    //     );

    //     /** @var array<string, mixed> $rendered */
    //     $rendered = $this->queryBuilder->render($model);

    //     return $rendered;
    // }

    // /**
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy(Request $request)
    // {
    //     $this->service->delete(
    //         $this->getModelFromRequest($request),
    //         $this->queryBuilder
    //     );

    //     return response('', 204);
    // }

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
