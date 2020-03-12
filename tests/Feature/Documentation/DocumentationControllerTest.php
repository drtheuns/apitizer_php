<?php

namespace Tests\Feature\Documentation;

use Apitizer\Apitizer;
use Tests\Support\Schemas\PostSchema;
use Tests\Feature\TestCase;

class DocumentationControllerTest extends TestCase
{
    /** @test */
    public function it_displays_all_the_expected_resources()
    {
        $response = $this->get(Apitizer::getRouteUrl())
                         ->assertOk()
                         ->assertSeeTextInOrder(['Post', 'Comment', 'User', 'Tag']);

        foreach (Apitizer::getSchemas() as $class) {
            $schema = new $class();

            $fields = collect($schema->getFields())->map->getName()->values();
            $response->assertSeeTextInOrder($fields->all());

            $filters = collect($schema->getFilters())->map->getName()->values();
            $response->assertSeeTextInOrder($filters->all());

            $sorts = collect($schema->getSorts())->map->getName()->values();
            $response->assertSeeTextInOrder($sorts->all());
        }

        $response->assertSeeText(PostSchema::DESCRIPTION);
    }
}
