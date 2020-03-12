<?php

namespace Apitizer;

use Apitizer\Routing\PendingSchemaRegistration;
use Apitizer\Routing\SchemaRoute;
use Illuminate\Routing\Router;

class RouteServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        Router::macro('schema', function (string $schema) {
            /** @var class-string<\Apitizer\Schema> $schema */
            return (new SchemaRoute($schema))->generateRoutes();
        });
    }
}
