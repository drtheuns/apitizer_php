<?php

namespace Tests;

use Illuminate\Http\Request;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function buildRequest($queryParameters = [], $resource = 'users', $method = 'GET'): Request
    {
        $request = Request::create($resource, $method);
        $request->merge($queryParameters);

        return $request;
    }

    protected function getEnvironmentSetUp($app)
    {
        // Use the default config when running tests.
        $app['config']->set('apitizer', require __DIR__.'/../config/apitizer.php');
    }
}
