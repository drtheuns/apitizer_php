<?php

namespace Apitizer\Routing;

use Apitizer\Exceptions\RouteDefinitionException;
use Apitizer\GenericApi\Controller;
use Apitizer\Schema;
use Illuminate\Routing\Route;

/**
 * Responsible for turning a schema into a set of routes.
 *
 * This class is used when you use the `schema` macro on the router:
 *
 *     Route::schema(PostSchema::class);
 *
 * Take a look at the RouteServiceProvider for the macro definition.
 */
class SchemaRoute
{
    /**
     * @var class-string<Schema>
     */
    protected $schema;

    /**
     * @var \Illuminate\Routing\Router;
     */
    protected $router;

    /**
     * @var Scope|null
     */
    protected $scope;

    /**
     * @param class-string<Schema> $schema
     */
    public function __construct(string $schema, Scope $scope = null)
    {
        $this->schema = $schema;
        $this->scope = $scope;
        $this->router = app('router');
    }

    /**
     * @return \Illuminate\Routing\Route[]
     */
    public function generateRoutes(): array
    {
        $schema = new $this->schema;

        if (! $schema instanceof Schema) {
            throw RouteDefinitionException::schemaExpected($this->schema);
        }

        $scope = $this->scope ?? $schema->getScope();

        return $this->generateRouteForScope($scope, $schema);
    }

    /**
     * @return \Illuminate\Routing\Route[] the routes that were registered
     */
    protected function generateRouteForScope(Scope $scope, Schema $schema): array
    {
        $routes = [];

        foreach ($scope->getAffordances() as $affordance) {
            foreach ($this->actionsForAffordance($affordance->getName()) as $actionMethod) {
                $routes[] = static::{'register'.ucfirst($actionMethod)}($scope, $affordance, $schema);
            }
        }

        $associations = $schema->getAssociations();

        // Recursively defines the routes for the associations.
        foreach ($scope->getAssociations() as $associationName => $childScope) {
            if (! isset($associations[$associationName])) {
                throw RouteDefinitionException::associationUndefined($associationName, $schema);
            }

            $association = $associations[$associationName];

            $routes = array_merge(
                $routes,
                $this->generateRouteForScope($childScope, $association->getRelatedSchema())
            );
        }

        return $routes;
    }

    public function registerIndex(Scope $scope, ScopeAffordance $affordance, Schema $schema): Route
    {
        $segments = new RouteSegmentBuilder($schema, $scope);
        $metadata = $this->metadata($schema, $affordance, $segments);

        return $this->router->match(['GET'], $segments->path(), [
            'uses'     => $this->controller('index'),
            'metadata' => $metadata,
        ])->name($segments->name('index'));
    }

    public function registerShow(Scope $scope, ScopeAffordance $affordance, Schema $schema): Route
    {
        $segments = new RouteSegmentBuilder($schema, $scope, true);
        $metadata = $this->metadata($schema, $affordance, $segments);

        return $this->router->match(['GET'], $segments->path(), [
            'uses'     => $this->controller('show'),
            'metadata' => $metadata,
        ])->name($segments->name('show'));
    }

    public function registerStore(Scope $scope, ScopeAffordance $affordance, Schema $schema): Route
    {
        $segments = new RouteSegmentBuilder($schema, $scope);
        $metadata = $this->metadata($schema, $affordance, $segments);

        return $this->router->match(['POST'], $segments->path(), [
            'uses'     => $this->controller('store'),
            'metadata' => $metadata,
        ])->name($segments->name('store'));
    }

    public function registerUpdate(Scope $scope, ScopeAffordance $affordance, Schema $schema): Route
    {
        $segments = new RouteSegmentBuilder($schema, $scope, true);
        $metadata = $this->metadata($schema, $affordance, $segments);

        return $this->router->match(['PUT', 'PATCH'], $segments->path(), [
            'uses'     => $this->controller('update'),
            'metadata' => $metadata,
        ])->name($segments->name('update'));
    }

    public function registerDestroy(Scope $scope, ScopeAffordance $affordance, Schema $schema): Route
    {
        $segments = new RouteSegmentBuilder($schema, $scope, true);
        $metadata = $this->metadata($schema, $affordance, $segments);

        return $this->router->match(['DELETE'], $segments->path(), [
            'uses'     => $this->controller('destroy'),
            'metadata' => $metadata,
        ])->name($segments->name('destroy'));
    }

    /**
     * @return string[]
     */
    protected function actionsForAffordance(string $affordance): array
    {
        switch ($affordance) {
            case 'readable':
                return ['index', 'show'];
            case 'creatable':
                return ['store'];
            case 'updatable':
                return ['update'];
            case 'deletable':
                return ['destroy'];
        }

        return [];
    }

    /**
     * @return array{schema: class-string<Schema>,
     *               service: string|null,
     *               service_method: string|null,
     *               routeParameters: array<string, array<string, string|bool>>}
     */
    protected function metadata(
        Schema $schema,
        ScopeAffordance $affordance,
        RouteSegmentBuilder $segments
    ): array {
        return [
            'schema' => get_class($schema),
            'service' => $affordance->getService(),
            'service_method' => $affordance->getMethod(),
            'routeParameters' => $segments->routeParameters(),
        ];
    }

    protected function controller(string $action): string
    {
        return '\\' . Controller::class . "@$action";
    }
}
