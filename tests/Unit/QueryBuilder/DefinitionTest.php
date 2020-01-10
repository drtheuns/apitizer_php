<?php

namespace Tests\Unit\QueryBuilder;

use Apitizer\Types\Field;
use Tests\Unit\TestCase;
use Tests\Feature\Builders\EmptyBuilder;

class DefinitionTest extends TestCase
{
    // The bindings are needed for this test.
    protected function getPackageProviders($app)
    {
        return ['Apitizer\ServiceProvider'];
    }

    /** @test */
    public function it_casts_string_fields_to_any_type()
    {
        $fields = (new AnyBuilder())->getFields();

        $this->assertCount(1, $fields);
        $this->assertInstanceOf(Field::class, $fields['any']);
        $this->assertEquals('any', $fields['any']->getType());
    }
}


class AnyBuilder extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'any' => 'any',
        ];
    }
}
