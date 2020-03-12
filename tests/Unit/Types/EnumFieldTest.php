<?php

namespace Tests\Unit\Types;

use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\Types\EnumField;
use Tests\Unit\TestCase;
use Tests\Support\Schemas\UserSchema;

class EnumFieldTest extends TestCase
{
    const DEFAULT = ['open', 'closed'];

    /** @test */
    public function it_raises_an_exception_when_a_value_is_rendered_that_is_not_in_the_enum()
    {
        $this->expectException(InvalidOutputException::class);

        $this->enumField(static::DEFAULT)->render(['key' => 'unexpected']);
    }

    private function enumField(array $enum, string $key = 'key', string $type = 'string')
    {
        return new EnumField(new UserSchema(), $key, $enum, $type);
    }
}
