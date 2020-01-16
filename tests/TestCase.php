<?php

namespace Tests;

use Tests\Support\Schema;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var array
     */
    protected $builderClasses;

    protected function getEnvironmentSetUp($app)
    {
        // Use the default config when running tests.
        $app['config']->set('apitizer', require __DIR__.'/../config/apitizer.php');
        $app['config']->set('apitizer.schema', Schema::class);
    }

    protected function request(string $method = null, string $url = null)
    {
        return new RequestBuilder($method, $url);
    }
}
