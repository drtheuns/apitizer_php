<?php

namespace Apitizer\Routing;

use Apitizer\GenericApi\Controller;
use Apitizer\GenericApi\ModelService;
use Apitizer\GenericApi\Service;
use Apitizer\QueryBuilder;
use Illuminate\Support\Facades\Route as LaravelRoute;
use Illuminate\Support\Str;
use Illuminate\Routing\Route as RouteInstance;

class Route
{
    /**
     * @var class-string<QueryBuilder>
     */
    protected $schema;

    /**
     * @var string[]|null
     */
    protected $only;

    /**
     * @var string[]|null
     */
    protected $except;

    /**
     * @var class-string
     */
    protected $controller;

    /**
     * @var class-string<Service>
     */
    protected $service;

    /**
     * @var array{schema: class-string<QueryBuilder>, service: class-string<Service>}
     */
    protected $metadata;

    /**
     * @var \Illuminate\Routing\Router;
     */
    protected $router;

    /**
     * @var string the name of the parameter in the route.
     */
    protected $paramName;

    /**
     * @var string the route url name.
     */
    protected $routeName;

    /**
     * @param class-string<QueryBuilder> $schema
     * @param array{only?: string[], except?: string[], controller?: class-string,
     *              service?: class-string<Service>} $options
     */
    public function __construct(string $schema, array $options)
    {
        $this->schema = $schema;
        $this->only = $options['only'] ?? null;
        $this->except = $options['except'] ?? null;
        $this->controller = $options['controller'] ?? '\\' . Controller::class;
        $this->service = $options['service'] ?? ModelService::class;
        $this->router = app('router');

        $className = class_basename($this->schema);
        $routeName = Str::endsWith($className, 'Builder') ? substr($className, 0, -7) : $className;
        $this->paramName = Str::slug($routeName);
        $this->routeName = Str::plural($this->paramName);
        $this->metadata = [
            'schema'         => $this->schema,
            'service'        => $this->service,
            'routeParamName' => $this->paramName,
        ];
    }

    /**
     * @return \Illuminate\Routing\Route[] the routes that were registered
     */
    public function register(): array
    {
        $routes = [];

        foreach ($this->getResourceActions() as $actionMethod) {
            $routes[] = static::{'register'.ucfirst($actionMethod)}();
        }

        return $routes;
    }

    /**
     * @return string[]
     */
    public function getResourceActions(): array
    {
        $methods = ['index', 'show', 'store', 'update', 'destroy'];

        if ($this->only) {
            $methods = array_intersect($methods, $this->only);
        }

        if ($this->except) {
            $methods = array_diff($methods, $this->except);
        }

        return $methods;
    }

    public function registerIndex(): RouteInstance
    {
        [$uses, $name] = $this->action('index');

        return $this->addRoute(['GET'], $this->routeName, $uses, $name);
    }

    public function registerShow(): RouteInstance
    {
        [$uses, $name] = $this->action('show');

        return $this->addRoute(['GET'], $this->routeWithParameter(), $uses, $name);
    }

    public function registerStore(): RouteInstance
    {
        [$uses, $name] = $this->action('store');

        return $this->addRoute(['POST'], $this->routeName, $uses, $name);
    }

    public function registerUpdate(): RouteInstance
    {
        [$uses, $name] = $this->action('update');

        return $this->addRoute(['PUT', 'PATCH'], $this->routeWithParameter(), $uses, $name);
    }

    public function registerDestroy(): RouteInstance
    {
        [$uses, $name] = $this->action('destroy');

        return $this->addRoute(['DELETE'], $this->routeWithParameter(), $uses, $name);
    }

    /**
     * @param string[] $methods
     * @param string $route
     * @param string $uses
     * @param string $name
     * @return \Illuminate\Routing\Route
     */
    protected function addRoute(
        array $methods,
        string $route,
        string $uses,
        string $name
    ): RouteInstance {
        return $this->router->match($methods, $route, [
            'uses' => $uses,
            'metadata' => $this->metadata,
        ])->name($name);
    }

    protected function routeWithParameter(): string
    {
        return $this->routeName . '/{' . $this->paramName . '}';
    }

    /**
     * @return string[]
     */
    protected function action(string $name): array
    {
        return [$this->controller . "@$name", $this->routeName . ".$name"];
    }
}
