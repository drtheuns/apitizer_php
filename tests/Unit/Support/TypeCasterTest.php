<?php

namespace Tests\Unit\Support;

use Apitizer\Support\TypeCaster;
use DateTime;
use DateTimeInterface;
use Tests\Unit\TestCase;

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
    public function nulls_will_remain_nulls()
    {
        $this->assertEquals(null, TypeCaster::cast(null, 'string'));
    }
}
