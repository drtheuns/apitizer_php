<?php

namespace Tests\Feature;

use Tests\Support\BuildsRequests;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use BuildsRequests, DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/database/factories');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return ['Apitizer\ServiceProvider'];
    }
}
