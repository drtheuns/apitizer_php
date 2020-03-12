<?php

namespace Tests\Feature\Documentation;

use Apitizer\Apitizer;
use Tests\Feature\TestCase;
use Tests\Support\Schemas;

class MissingDocumentationTest extends TestCase
{
    protected $schemaClasses = [
        Schemas\CommentSchema::class,
        Schemas\PostSchema::class,
        Schemas\UserSchema::class,
    ];

    /** @test */
    public function it_should_still_work_when_documentation_is_missing()
    {
        $response = $this->get(Apitizer::getRouteUrl())->assertOk();

        $response->assertDontSeeText('Tag');
        $response->assertSeeText('Documentation is missing');
    }
}
