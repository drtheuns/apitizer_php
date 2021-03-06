<?php

namespace Tests\Unit\Types;

use Apitizer\Exceptions\DefinitionException;
use Apitizer\Exceptions\InvalidInputException;
use Apitizer\Types\Filter;
use Tests\Support\Schemas\UserSchema;
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

        $filter = $this->filter()->expect()->array()->whereEach()->string();
        $filter->setValue("name");
    }

    /** @test */
    public function it_validates_the_type_of_the_input()
    {
        $this->expectException(InvalidInputException::class);

        $filter = $this->filter()->expect()->uuid();
        $filter->setValue(['should be uuid']);
    }

    /** @test */
    public function it_throws_an_exception_if_an_enumerator_was_expected_but_value_is_not_in_the_input_array()
    {
        $this->expectException(InvalidInputException::class);

        $filter = $this->filter()->expect()->enum(['orange', 'blue', 'green']);
        $filter->setValue('black');
    }

    /** @test */
    public function it_throws_an_exception_if_whereEach_is_called_without_expect(): void
    {
        $this->expectException(DefinitionException::class);

        $this->filter()->whereEach();
    }

    private function filter()
    {
        return new Filter(new UserSchema());
    }
}
