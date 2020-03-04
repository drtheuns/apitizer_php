<?php

namespace Apitizer;

use Apitizer\Routing\PendingSchemaRegistration;
use Illuminate\Routing\Router;

class RouteServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        Router::macro('schema', function (string $schema) {
            /** @var class-string<\Apitizer\QueryBuilder> $schema */
            return new PendingSchemaRegistration($schema);
        });
    }
}
