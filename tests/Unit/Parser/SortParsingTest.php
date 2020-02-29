<?php

namespace Tests\Unit\Parser;

use Apitizer\Exceptions\InvalidInputException;
use Apitizer\Parser\InputParser;
use Apitizer\Parser\ParsedInput;
use Apitizer\Parser\Sort;
use Tests\Unit\TestCase;

class SortParsingTest extends TestCase
{
    /** @test */
    public function it_can_parse_sorting_on_single_fields()
    {
        $sorts = $this->parse('name');

        $this->assertEquals(1, count($sorts));
        $this->assertInstanceOf(Sort::class, $sorts[0]);
        $this->assertEquals('name', $sorts[0]->getField());
        $this->assertEquals(Sort::ASC, $sorts[0]->getOrder());
    }

    /** @test */
    public function it_can_parse_sorting_with_order_directions()
    {
        $sorts = $this->parse('name.desc');

        $this->assertEquals(1, count($sorts));
        $this->assertInstanceOf(Sort::class, $sorts[0]);
        $this->assertEquals('name', $sorts[0]->getField());
        $this->assertEquals(Sort::DESC, $sorts[0]->getOrder());
    }

    /** @test */
    public function it_can_parse_multiple_comma_separated_sorts()
    {
        $sorts = $this->parse('name.desc,id.asc');

        $this->assertEquals(2, count($sorts));

        $this->assertInstanceOf(Sort::class, $sorts[0]);
        $this->assertInstanceOf(Sort::class, $sorts[1]);

        $this->assertEquals('name', $sorts[0]->getField());
        $this->assertEquals(Sort::DESC, $sorts[0]->getOrder());

        $this->assertEquals('id', $sorts[1]->getField());
        $this->assertEquals(Sort::ASC, $sorts[1]->getOrder());
    }

    /** @test */
    public function it_can_parse_sorts_as_arrays()
    {
        $sorts = $this->parse(['name.desc', 'id.asc']);

        $this->assertEquals(2, count($sorts));

        $this->assertInstanceOf(Sort::class, $sorts[0]);
        $this->assertInstanceOf(Sort::class, $sorts[1]);

        $this->assertEquals('name', $sorts[0]->getField());
        $this->assertEquals(Sort::DESC, $sorts[0]->getOrder());

        $this->assertEquals('id', $sorts[1]->getField());
        $this->assertEquals(Sort::ASC, $sorts[1]->getOrder());
    }

    /** @test */
    public function incorrect_sort_orders_are_replaced_with_ascending_order()
    {
        $sorts = $this->parse('name.upside-down');

        $this->assertEquals(Sort::ASC, $sorts[0]->getOrder());
    }

    /** @test */
    public function sorting_is_ignored_if_invalid_input_is_passed()
    {
        $sorts = $this->parse(1);

        $this->assertEmpty($sorts);
    }

    /** @test */
    public function an_exception_is_raised_if_an_invalid_array_is_passed()
    {
        $this->assertEquals([], $this->parse([[]]));
    }

    private function parse($sort)
    {
        $parsedInput = new ParsedInput;
        (new InputParser())->parseSorts($parsedInput, $sort);
        return $parsedInput->sorts;
    }
}
