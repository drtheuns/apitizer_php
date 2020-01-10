<?php

namespace Tests\Feature\Documentation;

use Apitizer\Apitizer;
use Tests\Feature\TestCase;
use Tests\Feature\Builders;

class MissingDocumentationTest extends TestCase
{
    /** @test */
    public function it_should_still_work_when_documentation_is_missing()
    {
        $response = $this->get(Apitizer::getRouteUrl())->assertOk();

        $response->assertDontSeeText('Tag');
        $response->assertSeeText('Documentation is missing');
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->builderClasses = [
            Builders\PostBuilder::class,
            Builders\CommentBuilder::class,
            Builders\UserBuilder::class,
            // Missing: TagBuilder
        ];

        $app['config']->set(
            'apitizer', require __DIR__ . '/../../../config/apitizer.php'
        );
        $app['config']->set('apitizer.query_builders', $this->builderClasses);
    }
}
