<?php

namespace Tests\Unit\Types;

use Apitizer\Transformers\CastValue;
use Apitizer\Types\DateTimeField;
use DateTimeInterface;
use Tests\Support\Builders\UserBuilder;
use Tests\Unit\TestCase;

class DateTimeFieldTest extends TestCase
{
    /** @test */
    public function a_datetime_field_can_be_formatted()
    {
        $formatted = $this->datetimeField()
                          ->transform(new CastValue)
                          ->transform(function ($value) {
                              // Validate that the value was actually cast.
                              $this->assertInstanceOf(DateTimeInterface::class, $value);
                              return $value;
                          })
                          ->format()
                          ->render(['key' => '2020-01-01 13:00:00']);

        $this->assertEquals('2020-01-01 13:00:00', $formatted);
    }

    /** @test */
    public function a_date_field_can_be_formatted()
    {
        $formatted = $this->dateField()
            ->transform(new CastValue)
            ->transform(function ($value) {
                // Validate that the value was actually cast.
                $this->assertInstanceOf(DateTimeInterface::class, $value);
                return $value;
            })
            ->format()
            ->render(['key' => '2020-01-01']);

        $this->assertEquals('2020-01-01', $formatted);
    }

    /** @test */
    public function custom_formats_are_supported()
    {
        $formatted = $this->datetimeField()
                          ->transform(new CastValue)
                          ->format('H:i:s, Y-m-d')
                          ->render(['key' => '2020-01-01 13:00:00']);

        $this->assertEquals('13:00:00, 2020-01-01', $formatted);
    }

    private function datetimeField(string $key = 'key'): DateTimeField
    {
        return new DateTimeField(new UserBuilder(), $key, 'datetime');
    }

    private function dateField(string $key = 'key'): DateTimeField
    {
        return new DateTimeField(new UserBuilder(), $key, 'date');
    }
}
