<?php

namespace Tests\Feature\Documentation;

use Apitizer\Apitizer;
use Tests\Feature\Builders\PostBuilder;
use Tests\Feature\TestCase;
use Tests\Feature\Builders;

class DocumentationControllerTest extends TestCase
{
    /** @test */
    public function it_displays_all_the_expected_resources()
    {
        $response = $this->get(Apitizer::getRouteUrl())
                         ->assertOk()
                         ->assertSeeTextInOrder(['Post', 'Comment', 'User', 'Tag']);

        foreach ($this->builderClasses as $class) {
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
