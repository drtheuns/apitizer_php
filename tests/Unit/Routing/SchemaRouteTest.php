<?php

namespace Tests\Unit\Routing;

use Apitizer\GenericApi\ModelService;
use Apitizer\Routing\SchemaRoute;
use Apitizer\Routing\Scope;
use Tests\Support\Builders\UserBuilder;
use Tests\Unit\TestCase;
use Illuminate\Support\Facades\Route as LaravelRoute;

class SchemaRouteTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        // This is needed to register the router macro.
        return ['Apitizer\RouteServiceProvider'];
    }

    /** @test */
    public function it_creates_the_index_route(): void
    {
        $scope = (new Scope)->readable();
        $route = (new SchemaRoute(UserBuilder::class))->registerIndex(
            $scope, $scope->getAffordances()['readable'], new UserBuilder
        );

        $this->assertEquals($route->uri, 'users');
        $this->assertEquals($route->methods, ['GET', 'HEAD']);
        $this->assertEquals($route->action['as'], 'users.index');
    }

    /** @test */
    public function it_creates_the_show_route(): void
    {
        $scope = (new Scope)->readable();
        $route = (new SchemaRoute(UserBuilder::class))->registerShow(
            $scope, $scope->getAffordances()['readable'], new UserBuilder
        );

        $this->assertEquals($route->uri, 'users/{user}');
        $this->assertEquals($route->methods, ['GET', 'HEAD']);
        $this->assertEquals($route->action['as'], 'users.show');
    }

    /** @test */
    public function it_creates_the_store_route(): void
    {
        $scope = (new Scope)->creatable();
        $route = (new SchemaRoute(UserBuilder::class))->registerStore(
            $scope, $scope->getAffordances()['creatable'], new UserBuilder
        );

        $this->assertEquals($route->uri, 'users');
        $this->assertEquals($route->methods, ['POST']);
        $this->assertEquals($route->action['as'], 'users.store');
    }

    /** @test */
    public function it_creates_the_update_route(): void
    {
        $scope = (new Scope)->updatable();
        $route = (new SchemaRoute(UserBuilder::class))->registerUpdate(
            $scope, $scope->getAffordances()['updatable'], new UserBuilder
        );

        $this->assertEquals($route->uri, 'users/{user}');
        $this->assertEquals($route->methods, ['PUT', 'PATCH']);
        $this->assertEquals($route->action['as'], 'users.update');
    }

    /** @test */
    public function it_creates_the_destroy_route(): void
    {
        $scope = (new Scope)->deletable();
        $route = (new SchemaRoute(UserBuilder::class))->registerDestroy(
            $scope, $scope->getAffordances()['deletable'], new UserBuilder
        );

        $this->assertEquals($route->uri, 'users/{user}');
        $this->assertEquals($route->methods, ['DELETE']);
        $this->assertEquals($route->action['as'], 'users.destroy');
    }

    /** @test */
    public function it_registers_all_routes(): void
    {
        $scope = (new Scope)->crud();
        $routes = (new SchemaRoute(UserBuilder::class))->generateRoutes($scope, new UserBuilder);

        $this->assertCount(5, $routes);
    }

    // /** @test */
    // public function it_stores_metadata_in_the_route_action(): void
    // {
    //     $route = (new Route(UserBuilder::class, []))->registerShow();

    //     $this->assertArrayHasKey('metadata', $route->action);
    //     $this->assertEquals([
    //         'schema'         => UserBuilder::class,
    //         'service'        => ModelService::class,
    //         'routeParamName' => 'user',
    //     ], $route->action['metadata']);
    // }

    // /** @test */
    // public function it_allows_a_different_controller_to_be_passed(): void
    // {
    //     $controller = '\App\Http\Controllers\MyOwnController';
    //     $route = (new Route(UserBuilder::class, ['controller' => $controller]))->registerShow();

    //     $this->assertEquals($controller . '@show', $route->action['uses']);
    // }

    // /** @test */
    // public function it_allows_a_different_service_to_be_passed(): void
    // {
    //     $service = '\App\Services\MyOwnService';
    //     $route = (new Route(UserBuilder::class, ['service' => $service]))->registerShow();

    //     $this->assertEquals($service, $route->action['metadata']['service']);
    // }

    // /** @test */
    // public function the_routes_are_registered_in_the_router(): void
    // {
    //     // This test is primarily a sanity check that the routes are actually
    //     // registered with the router, rather than just instantiated but not
    //     // stored.
    //     $routes = (new Route(UserBuilder::class, []))->register();

    //     $this->assertCount(5, $this->getRegisteredRoutes());
    // }

    // /** @test */
    // public function routes_can_be_registered_using_the_schema_macro(): void
    // {
    //     LaravelRoute::schema(UserBuilder::class);

    //     $this->assertCount(5, $this->getRegisteredRoutes());
    // }

    // /** @test */
    // public function only_allows_only_certain_routes_to_be_registered(): void
    // {
    //     LaravelRoute::schema(UserBuilder::class)->only(['show']);

    //     $this->assertCount(1, $this->getRegisteredRoutes());
    // }

    // /** @test */
    // public function except_allows_routes_to_be_excluded_from_registration(): void
    // {
    //     LaravelRoute::schema(UserBuilder::class)->except(['show']);

    //     $this->assertCount(4, $this->getRegisteredRoutes());
    // }

    // /** @test */
    // public function the_service_and_controller_can_be_when_using_the_route_builder(): void
    // {
    //     $controller = '\App\Http\Controllers\MyController';
    //     $service = '\App\Services\UserService';

    //     LaravelRoute::schema(UserBuilder::class)
    //         ->only(['show'])
    //         ->controller($controller)
    //         ->service($service);

    //     $routeCollection = app('router')->getRoutes();
    //     $this->assertCount(1, $routeCollection->getRoutes());

    //     $route = $routeCollection->getByAction('App\Http\Controllers\MyController@show');

    //     $this->assertNotNull($route);
    //     $this->assertEquals($service, $route->action['metadata']['service']);
    // }

    // private function getRegisteredRoutes()
    // {
    //     return app('router')->getRoutes()->getRoutes();
    // }
}
