<?php

namespace Tests\Feature\Documentation;

use Apitizer\Apitizer;
use Tests\Feature\TestCase;
use Tests\Support\Builders;

class MissingDocumentationTest extends TestCase
{
    protected $builderClasses = [
        Builders\CommentBuilder::class,
        Builders\PostBuilder::class,
        Builders\UserBuilder::class,
    ];

    /** @test */
    public function it_should_still_work_when_documentation_is_missing()
    {
        $response = $this->get(Apitizer::getRouteUrl())->assertOk();

        $response->assertDontSeeText('Tag');
        $response->assertSeeText('Documentation is missing');
    }
}
