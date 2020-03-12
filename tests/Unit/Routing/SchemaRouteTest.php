<?php

namespace Tests\Unit\Routing;

use Apitizer\Exceptions\RouteDefinitionException;
use Apitizer\GenericApi\ModelService;
use Apitizer\Routing\SchemaRoute;
use Apitizer\Routing\Scope;
use Tests\Support\Schemas\EmptySchema;
use Tests\Support\Schemas\UserSchema;
use Tests\Unit\TestCase;
use Illuminate\Support\Facades\Route as LaravelRoute;

class SchemaRouteTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // Disable the doc route, to not confuse the tests.
        $app['config']->set('apitizer.generate_documentation', false);
    }

    protected function getPackageProviders($app)
    {
        return ['Apitizer\ServiceProvider'];
    }

    /** @test */
    public function it_creates_the_index_route(): void
    {
        $scope = (new Scope)->readable();
        $route = (new SchemaRoute(UserSchema::class))->registerIndex(
            $scope, $scope->getAffordances()['readable'], new UserSchema
        );

        $this->assertEquals($route->uri, 'users');
        $this->assertEquals($route->methods, ['GET', 'HEAD']);
        $this->assertEquals($route->action['as'], 'users.index');
    }

    /** @test */
    public function it_creates_the_show_route(): void
    {
        $scope = (new Scope)->readable();
        $route = (new SchemaRoute(UserSchema::class))->registerShow(
            $scope, $scope->getAffordances()['readable'], new UserSchema
        );

        $this->assertEquals($route->uri, 'users/{user}');
        $this->assertEquals($route->methods, ['GET', 'HEAD']);
        $this->assertEquals($route->action['as'], 'users.show');
    }

    /** @test */
    public function it_creates_the_store_route(): void
    {
        $scope = (new Scope)->creatable();
        $route = (new SchemaRoute(UserSchema::class))->registerStore(
            $scope, $scope->getAffordances()['creatable'], new UserSchema
        );

        $this->assertEquals($route->uri, 'users');
        $this->assertEquals($route->methods, ['POST']);
        $this->assertEquals($route->action['as'], 'users.store');
    }

    /** @test */
    public function it_creates_the_update_route(): void
    {
        $scope = (new Scope)->updatable();
        $route = (new SchemaRoute(UserSchema::class))->registerUpdate(
            $scope, $scope->getAffordances()['updatable'], new UserSchema
        );

        $this->assertEquals($route->uri, 'users/{user}');
        $this->assertEquals($route->methods, ['PUT', 'PATCH']);
        $this->assertEquals($route->action['as'], 'users.update');
    }

    /** @test */
    public function it_creates_the_destroy_route(): void
    {
        $scope = (new Scope)->deletable();
        $route = (new SchemaRoute(UserSchema::class))->registerDestroy(
            $scope, $scope->getAffordances()['deletable'], new UserSchema
        );

        $this->assertEquals($route->uri, 'users/{user}');
        $this->assertEquals($route->methods, ['DELETE']);
        $this->assertEquals($route->action['as'], 'users.destroy');
    }

    /** @test */
    public function it_registers_all_routes(): void
    {
        $scope = (new Scope)->crud();
        $routes = (new SchemaRoute(UserSchema::class, $scope))->generateRoutes();

        $this->assertCount(5, $routes);
    }

    /** @test */
    public function it_stores_metadata_in_the_route_action(): void
    {
        $scope = (new Scope)->readable();
        $affordance = $scope->getAffordances()['readable'];
        $route = (new SchemaRoute(UserSchema::class))->registerShow($scope, $affordance, new UserSchema);

        $this->assertArrayHasKey('metadata', $route->action);
        $this->assertEquals([
            'schema'         => UserSchema::class,
            'service'        => null,
            'service_method' => null,
            'routeParameters' => [
                'users' => [
                    'schema'      => UserSchema::class,
                    'has_param'   => false,
                    'association' => null,
                ],
                'user' => [
                    'schema'      => UserSchema::class,
                    'has_param'   => true,
                    'association' => null
                ]
            ]
        ], $route->action['metadata']);
    }

    /** @test */
    public function it_allows_a_different_service_to_be_passed(): void
    {
        $service = 'App\Services\MyOwnService';
        $scope = (new Scope)->creatable($service);
        $affordance = $scope->getAffordances()['creatable'];
        $route = (new SchemaRoute(UserSchema::class))->registerStore($scope, $affordance, new UserSchema);

        $this->assertEquals($service, $route->action['metadata']['service']);
    }

    /** @test */
    public function the_routes_are_registered_in_the_router(): void
    {
        // This test is primarily a sanity check that the routes are actually
        // registered with the router, rather than just instantiated but not
        // stored.
        $scope = (new Scope)->crud();
        $routes = (new SchemaRoute(UserSchema::class, $scope))->generateRoutes();

        $this->assertCount(5, $this->getRegisteredRoutes());
    }

    /** @test */
    public function routes_can_be_registered_using_the_schema_macro(): void
    {
        LaravelRoute::schema(CrudSchema::class);

        $this->assertCount(5, $this->getRegisteredRoutes());
    }

    /** @test */
    public function it_throws_an_exception_if_the_given_class_is_not_a_schema(): void
    {
        $this->expectException(RouteDefinitionException::class);

        LaravelRoute::schema(self::class);
    }

    /** @test */
    public function it_throws_an_exception_if_the_association_does_not_exist_on_the_schema(): void
    {
        $this->expectException(RouteDefinitionException::class);

        $scope = (new Scope)->associationCrud('author');

        (new SchemaRoute(EmptySchema::class, $scope))->generateRoutes();
    }

    private function getRegisteredRoutes()
    {
        return app('router')->getRoutes()->getRoutes();
    }
}

class CrudSchema extends EmptySchema
{
    public function scope(Scope $scope)
    {
        $scope->crud();
    }
}
