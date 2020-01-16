<?php

namespace Tests;

use Tests\Support\Builders;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var string[]
     */
    protected $builderClasses = [
        Builders\CommentBuilder::class,
        Builders\PostBuilder::class,
        Builders\UserBuilder::class,
        Builders\TagBuilder::class,
    ];

    protected function getEnvironmentSetUp($app)
    {
        // Use the default config when running tests.
        $app['config']->set('apitizer', require __DIR__.'/../config/apitizer.php');
        $app['config']->set('apitizer.query_builders', [
            'classes' => $this->builderClasses,
            'namespaces' => [],
        ]);
    }

    protected function request(string $method = null, string $url = null)
    {
        return new RequestBuilder($method, $url);
    }
}
