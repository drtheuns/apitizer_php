<?php

namespace Tests\Unit\Types;

use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\Types\Field;
use Apitizer\Types\GeneratedField;
use ArrayAccess;
use Tests\Unit\TestCase;
use Tests\Support\Schemas\UserSchema;

class FieldTest extends TestCase
{
    /** @test */
    public function it_accepts_array_accessible_objects_when_rendering()
    {
        // E.g. Eloquent models, collections, etc
        $collection = collect(['key' => 'value']);
        $rendered = $this->field()->render($collection);

        $this->assertInstanceOf(ArrayAccess::class, $collection);
        $this->assertEquals('value', $rendered);
    }

    /** @test */
    public function non_nullable_fields_throw_when_null_value_is_rendered()
    {
        $this->expectException(InvalidOutputException::class);
        $this->field()->render(['key' => null]);
    }

    /** @test */
    public function nullable_fields_dont_throw_when_null_value_is_rendered()
    {
        $rendered = $this->field()->nullable()->render(['key' => null]);

        $this->assertEquals(null, $rendered);
    }

    /** @test */
    public function generated_fields_render_data_from_a_callback()
    {
        $field = new GeneratedField(new UserSchema(), 'string', function () {
            return 'hello';
        });

        $rendered = $field->render([]);

        $this->assertEquals('hello', $rendered);
    }

    private function field(string $key = 'key', string $type = 'string')
    {
        return new Field(new UserSchema(), $key, $type);
    }
}
