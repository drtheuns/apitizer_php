<?php

namespace Tests;

use Illuminate\Http\Request;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        // Use the default config when running tests.
        $app['config']->set('apitizer', require __DIR__.'/../config/apitizer.php');
    }

    protected function request(string $method = null, string $url = null)
    {
        return new RequestBuilder($method, $url);
    }
}
