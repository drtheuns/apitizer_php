<?php

namespace Tests\Unit\Support;

use Apitizer\Support\TypeCaster;
use DateTime;
use DateTimeInterface;
use Tests\Unit\TestCase;
use UnexpectedValueException;

class TypeCasterTest extends TestCase
{
    /** @test */
    public function it_can_cast_strings_to_date()
    {
        $date = TypeCaster::cast('2020-01-01', 'date');

        $this->assertInstanceOf(DateTimeInterface::class, $date);
        $this->assertEquals(DateTime::createFromFormat('Y-m-d', '2020-01-01'), $date);
    }

    /** @test */
    public function it_can_cast_string_to_datetime()
    {
        $datetime = TypeCaster::cast('2020-01-01 19:00:00', 'datetime');

        $this->assertInstanceOf(DateTimeInterface::class, $datetime);
        $this->assertEquals(DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 19:00:00'), $datetime);
    }

    /** @test */
    public function custom_formats_may_be_passed_to_datetime_casting()
    {
        $datetime = TypeCaster::cast('19:00:00, 2020-01-01', 'datetime', 'H:i:s, Y-m-d');

        $this->assertInstanceOf(DateTimeInterface::class, $datetime);
        $this->assertEquals(DateTime::createFromFormat('Y-m-d H:i:s', '2020-01-01 19:00:00'), $datetime);
    }

    /** @test */
    public function it_returns_the_value_if_it_is_already_a_datetime_object()
    {
        $original = new DateTime();
        $datetime = TypeCaster::cast($original, 'datetime');

        $this->assertSame($original, $datetime);
    }

    /** @test */
    public function it_throws_an_exception_if_datetime_casting_receives_unexpected_type()
    {
        $this->expectException(UnexpectedValueException::class);
        TypeCaster::cast(1, 'datetime');
    }

    /** @test */
    public function unknown_types_are_ignored_and_return_the_value_unmodified()
    {
        $this->assertEquals(1, TypeCaster::cast(1, 'tree'));
        $this->assertEquals(2, TypeCaster::cast(2, 'uint_8'));
        $this->assertEquals(3, TypeCaster::cast(3, 'Maybe'));
    }

    /** @test */
    public function it_can_cast_various_boolean_types()
    {
        $true = ['on', 'yes', 'true', 1, '1'];
        $false = ['off', 'no', 'false', 0, '0', 'unexpected', null];

        foreach ($true as $value) {
            $this->assertTrue(TypeCaster::cast($value, 'bool'));
        }

        foreach ($false as $value) {
            $this->assertFalse(TypeCaster::cast($value, 'bool'));
        }
    }

    /** @test */
    public function it_can_cast_to_floating_point_numbers()
    {
        $this->assertEquals(1.0, TypeCaster::cast(1, 'float'));
        $this->assertEquals(1.0, TypeCaster::cast('1', 'float'));
        $this->assertEquals(1.0, TypeCaster::cast(1.0, 'float'));
    }

    /** @test */
    public function nulls_will_remain_nulls()
    {
        $this->assertEquals(null, TypeCaster::cast(null, 'string'));
    }
}
