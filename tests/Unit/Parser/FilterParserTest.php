<?php

namespace Tests\Unit\Parser;

use Apitizer\Parser\InputParser;
use Tests\Unit\TestCase;

class FilterParserTest extends TestCase
{
    /** @test */
    public function input_other_than_an_array_is_rejected()
    {
        $this->assertEquals(['key' => 'value'], $this->parse(['key' => 'value']));
        $this->assertEquals([], $this->parse('wow'));
        $this->assertEquals([], $this->parse(1));
        $this->assertEquals([], $this->parse(1.0));
        $this->assertEquals([], $this->parse((object) ['key' => 'value']));
        $this->assertEquals([], $this->parse(['key', 'value', 'whoops']));
    }

    private function parse($data)
    {
        return (new InputParser)->parseFilters($data);
    }
}
