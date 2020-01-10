<?php

namespace Tests\Feature\Documentation;

use Apitizer\Apitizer;
use Tests\Feature\TestCase;

class DisabledDocumentationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('apitizer.generate_documentation', false);
    }

    /** @test */
    public function the_documentation_route_is_not_available_with_docs_disabled()
    {
        $this->get(Apitizer::getRouteUrl())->assertNotFound();
    }
}
