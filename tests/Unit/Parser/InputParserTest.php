<?php

namespace Tests\Unit\Parser;

use Apitizer\Parser\InputParser;
use Apitizer\Parser\ParsedInput;
use Apitizer\Parser\RawInput;
use Tests\Unit\TestCase;

/*
 * The individual parser functions (fields, sorts, filters) are tested in
 * separate classes.
 */

class InputParserTest extends TestCase
{
    /** @test */
    public function it_parses_requests()
    {
        $request = $this->request()
                 ->fields('id, name, posts(id, title, body, comments(id, body))')
                 ->filter('name', 'Hornstock')
                 ->sort('name.asc,id.asc')
                 ->make();

        $input = (new InputParser())->parse(RawInput::fromRequest($request));

        $this->assertInstanceOf(ParsedInput::class, $input);
        $this->assertEquals(3, count($input->fields));
        $this->assertEquals(['name' => 'Hornstock'], $input->filters);
        $this->assertEquals(2, count($input->sorts));
    }
}
