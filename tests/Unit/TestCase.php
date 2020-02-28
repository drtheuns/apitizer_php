<?php

namespace Tests\Unit;

class TestCase extends \Tests\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Ensure translations are available in our tests
        $this->app['translator']->addNamespace('apitizer', __DIR__.'/../../resources/lang');
    }
}
