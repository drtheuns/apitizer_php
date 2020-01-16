<?php

namespace Tests\Unit\Types;

use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\Types\Field;
use ArrayAccess;
use Tests\Unit\TestCase;
use Tests\Support\Builders\UserBuilder;

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

    private function field(string $key = 'key', string $type = 'string')
    {
        return new Field(new UserBuilder(), $key, $type);
    }
}
