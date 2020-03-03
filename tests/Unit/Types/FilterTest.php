<?php

namespace Tests\Unit\Types;

use Apitizer\Exceptions\InvalidInputException;
use Apitizer\Types\Filter;
use Tests\Support\Builders\UserBuilder;
use Tests\Unit\TestCase;

class FilterTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_the_input_is_an_unexpected_array()
    {
        $this->expectException(InvalidInputException::class);

        $filter = $this->filter()->expect()->string();
        $filter->setValue(['name']);
    }

    /** @test */
    public function it_throws_an_exception_if_an_array_was_expected_but_not_given()
    {
        $this->expectException(InvalidInputException::class);

        $filter = $this->filter()->expectMany('string')->string();
        $filter->setValue('name');
    }

    /** @test */
    public function it_validates_the_type_of_the_input()
    {
        $this->expectException(InvalidInputException::class);

        $filter = $this->filter()->expectMany('uuid')->uuid();
        $filter->setValue(['should be uuid']);
    }

    private function filter()
    {
        return new Filter(new UserBuilder());
    }
}
