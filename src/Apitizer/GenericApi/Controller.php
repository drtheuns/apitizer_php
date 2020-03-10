<?php

namespace Apitizer\GenericApi;

use Apitizer\QueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     *            routeParameters: array<string, class-string>}
     */
    protected $metadata;

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
         *            routeParameters: array<string, class-string>} $metadata
         */
        $metadata = $route->action['metadata'];

        $schema = $metadata['schema'];
        $this->queryBuilder = new $schema($request);
        $this->metadata = $metadata;

        dd($metadata);
    }

    /**
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return $this->queryBuilder->paginate();
    }

    /**
     * @return array<string, mixed>
     */
    public function show(Request $request)
    {
        $model = $this->getModelFromRequest($request);

        /** @var array<string, mixed> $rendered */
        $rendered = $this->queryBuilder->render($model);

        return $rendered;
    }

    /**
     * @return array<string, mixed>
     */
    public function store(Request $request)
    {
        $model = $this->service->create(
            $this->queryBuilder->validated(),
            $this->queryBuilder
        );

        /** @var array<string, mixed> $rendered */
        $rendered = $this->queryBuilder->render($model);

        return $rendered;
    }

    /**
     * @return array<string, mixed>
     */
    public function update(Request $request)
    {
        $model = $this->service->update(
            $this->getModelFromRequest($request),
            $this->queryBuilder->validated(),
            $this->queryBuilder
        );

        /** @var array<string, mixed> $rendered */
        $rendered = $this->queryBuilder->render($model);

        return $rendered;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $this->service->delete(
            $this->getModelFromRequest($request),
            $this->queryBuilder
        );

        return response('', 204);
    }

    /**
     * @param Request $request
     * @param null|class-string $model
     * @return Model
     */
    protected function getModelFromRequest(Request $request, string $model = null): Model
    {
        // The logic in this method is based on the authorizeResource
        // middleware, which also fetches a model based on the class name and
        // the route, as well as the implicit route binding resolver.
        /** @var Model $model */
        $model = $model ? new $model : $this->queryBuilder->model();
        $modelClass = get_class($model);

        // This is used in the \Illuminate\Auth\Middleware\Authorize::getModel
        $value = $request->route($this->parameterName, null);

        // This logic is taken from the RouteBinding::getModel method.
        if (! $resolvedModel = $model->resolveRouteBinding($value)) {
            throw (new ModelNotFoundException)->setModel($modelClass);
        }

        return $resolvedModel;
    }
}
