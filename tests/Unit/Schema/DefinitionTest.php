<?php

namespace Tests\Unit\Schema;

use Apitizer\Types\Field;
use Tests\Unit\TestCase;
use Tests\Support\Schemas\EmptySchema;

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
        $fields = (new AnySchema())->getFields();

        $this->assertCount(1, $fields);
        $this->assertInstanceOf(Field::class, $fields['any']);
        $this->assertEquals('any', $fields['any']->getType());
    }
}


class AnySchema extends EmptySchema
{
    public function fields(): array
    {
        return [
            'any' => 'any',
        ];
    }
}
