<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestCase extends \Tests\TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/database/factories');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return ['Apitizer\ServiceProvider', 'Apitizer\RouteServiceProvider'];
    }
}
