<?php

namespace Tests;

use Tests\Feature\Builders;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var array
     */
    protected $builderClasses;

    protected function getEnvironmentSetUp($app)
    {
        $this->builderClasses = [
            Builders\PostBuilder::class,
            Builders\CommentBuilder::class,
            Builders\UserBuilder::class,
            Builders\TagBuilder::class,
        ];

        // Use the default config when running tests.
        $app['config']->set('apitizer', require __DIR__.'/../config/apitizer.php');
        $app['config']->set('apitizer.query_builders', $this->builderClasses);
    }

    protected function request(string $method = null, string $url = null)
    {
        return new RequestBuilder($method, $url);
    }
}
