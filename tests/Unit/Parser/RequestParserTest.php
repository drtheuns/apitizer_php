<?php

namespace Tests\Unit\Parser;

use Apitizer\RequestParser;
use Apitizer\Types\RequestInput;
use Tests\Unit\TestCase;

/*
 * The individual parser functions (fields, sorts, filters) are tested in
 * separate classes.
 */

class RequestParserTest extends TestCase
{
    /** @test */
    public function it_parses_requests()
    {
        $request = $this->buildRequest([
            'fields'  => 'id, name, posts(id, title, body, comments(id, body))',
            'filters' => ['name' => 'Hornstock'],
            'sort'    => 'name.asc,id.asc'
        ]);

        $input = (new RequestParser())->parse($request);

        $this->assertInstanceOf(RequestInput::class, $input);
        $this->assertEquals(3, count($input->fields));
        $this->assertEquals(['name' => 'Hornstock'], $input->filters);
        $this->assertEquals(2, count($input->sorts));
    }
}
