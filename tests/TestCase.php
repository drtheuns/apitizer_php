<?php

namespace Tests;

use Tests\Support\Schemas;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var string[]
     */
    protected $schemaClasses = [
        Schemas\CommentSchema::class,
        Schemas\PostSchema::class,
        Schemas\UserSchema::class,
        Schemas\TagSchema::class,
    ];

    protected function getEnvironmentSetUp($app)
    {
        // Use the default config when running tests.
        $app['config']->set('apitizer', require __DIR__.'/../config/apitizer.php');
        $app['config']->set('apitizer.schemas', [
            'classes' => $this->schemaClasses,
            'namespaces' => [],
        ]);
    }

    protected function request(string $method = null, string $url = null)
    {
        return new RequestBuilder($method, $url);
    }
}
