<?php

namespace Tests\Unit\Routing;

use Apitizer\GenericApi\ModelService;
use Apitizer\Routing\Route;
use Tests\Support\Builders\UserBuilder;
use Tests\Unit\TestCase;

class RouteTest extends TestCase
{
    /** @test */
    public function it_creates_the_index_route(): void
    {
        $route = (new Route(UserBuilder::class, []))->registerIndex();

        $this->assertEquals($route->uri, 'users');
        $this->assertEquals($route->methods, ['GET', 'HEAD']);
        $this->assertEquals($route->action['as'], 'users.index');
    }

    /** @test */
    public function it_creates_the_show_route(): void
    {
        $route = (new Route(UserBuilder::class, []))->registerShow();

        $this->assertEquals($route->uri, 'users/{user}');
        $this->assertEquals($route->methods, ['GET', 'HEAD']);
        $this->assertEquals($route->action['as'], 'users.show');
    }

    /** @test */
    public function it_creates_the_store_route(): void
    {
        $route = (new Route(UserBuilder::class, []))->registerStore();

        $this->assertEquals($route->uri, 'users');
        $this->assertEquals($route->methods, ['POST']);
        $this->assertEquals($route->action['as'], 'users.store');
    }

    /** @test */
    public function it_creates_the_update_route(): void
    {
        $route = (new Route(UserBuilder::class, []))->registerUpdate();

        $this->assertEquals($route->uri, 'users/{user}');
        $this->assertEquals($route->methods, ['PUT', 'PATCH']);
        $this->assertEquals($route->action['as'], 'users.update');
    }

    /** @test */
    public function it_creates_the_destroy_route(): void
    {
        $route = (new Route(UserBuilder::class, []))->registerDestroy();

        $this->assertEquals($route->uri, 'users/{user}');
        $this->assertEquals($route->methods, ['DELETE']);
        $this->assertEquals($route->action['as'], 'users.destroy');
    }

    /** @test */
    public function it_registers_all_routes(): void
    {
        $routes = (new Route(UserBuilder::class, []))->register();

        $this->assertCount(5, $routes);
    }

    /** @test */
    public function it_stores_metadata_in_the_route_action(): void
    {
        $route = (new Route(UserBuilder::class, []))->registerShow();

        $this->assertArrayHasKey('metadata', $route->action);
        $this->assertEquals([
            'schema'         => UserBuilder::class,
            'service'        => ModelService::class,
            'routeParamName' => 'user',
        ], $route->action['metadata']);
    }

    /** @test */
    public function it_allows_a_different_controller_to_be_passed(): void
    {
        $controller = '\App\Http\Controllers\MyOwnController';
        $route = (new Route(UserBuilder::class, ['controller' => $controller]))->registerShow();

        $this->assertEquals($controller . '@show', $route->action['uses']);
    }

    /** @test */
    public function it_allows_a_different_service_to_be_passed(): void
    {
        $service = '\App\Services\MyOwnService';
        $route = (new Route(UserBuilder::class, ['service' => $service]))->registerShow();

        $this->assertEquals($service, $route->action['metadata']['service']);
    }

    /** @test */
    public function the_routes_are_registered_in_the_router(): void
    {
        // This test is primarily a sanity check that the routes are actually
        // registered with the router, rather than just instantiated but not
        // stored.
        $routes = (new Route(UserBuilder::class, []))->register();
        $routeCollection = app('router')->getRoutes();

        $this->assertCount(5, $routeCollection->getRoutes());
    }
}
