<?php

namespace Tests\Feature\Documentation;

use Apitizer\Apitizer;
use Apitizer\Schema;
use Tests\Feature\TestCase;
use Tests\Support\Builders;

class MissingDocumentationTest extends TestCase
{
    /** @test */
    public function it_should_still_work_when_documentation_is_missing()
    {
        $this->mock(Schema::class, function ($schema) {
            $schema->shouldReceive('getQueryBuilders')
                   ->once()
                   ->andReturn([
                        Builders\PostBuilder::class,
                        Builders\CommentBuilder::class,
                        Builders\UserBuilder::class,
                   ]);
        });

        $response = $this->get(Apitizer::getRouteUrl())->assertOk();

        $response->assertDontSeeText('Tag');
        $response->assertSeeText('Documentation is missing');
    }
}
