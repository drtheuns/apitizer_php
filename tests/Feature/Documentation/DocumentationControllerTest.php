<?php

namespace Tests\Feature\Documentation;

use Apitizer\Apitizer;
use Tests\Support\Builders\PostBuilder;
use Tests\Feature\TestCase;

class DocumentationControllerTest extends TestCase
{
    /** @test */
    public function it_displays_all_the_expected_resources()
    {
        $response = $this->get(Apitizer::getRouteUrl())
                         ->assertOk()
                         ->assertSeeTextInOrder(['Post', 'Comment', 'User', 'Tag']);

        foreach (Apitizer::getQueryBuilders() as $class) {
            $builder = new $class();

            $fields = collect($builder->getFields())->map->getName()->values();
            $response->assertSeeTextInOrder($fields->all());

            $filters = collect($builder->getFilters())->map->getName()->values();
            $response->assertSeeTextInOrder($filters->all());

            $sorts = collect($builder->getSorts())->map->getName()->values();
            $response->assertSeeTextInOrder($sorts->all());
        }

        $response->assertSeeText(PostBuilder::DESCRIPTION);
    }
}
