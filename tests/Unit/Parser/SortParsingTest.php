<?php

namespace Tests\Unit\Parser;

use Apitizer\RequestParser;
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

    private function parse($sort)
    {
        return (new RequestParser())->parseSorts($sort);
    }
}
