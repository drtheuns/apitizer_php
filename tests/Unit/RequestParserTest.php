<?php

namespace Tests\Unit;

use Apitizer\RequestParser;
use Apitizer\Parser\Relation;

class RequestParserTest extends TestCase
{
    /** @test */
    public function it_should_parse_non_nested_fields()
    {
        $request = $this->buildRequest(['fields' => 'id,name']);

        $fields = (new RequestParser())->parseFields($request);

        $this->assertEquals(2, count($fields));
        $this->assertEquals($fields, ['id', 'name']);
    }

    /** @test */
    public function it_parses_relationships_fields()
    {
        $request = $this->buildRequest(['fields' => 'id,name,comments(id,body)']);

        $fields = (new RequestParser())->parseFields($request);

        $this->assertEquals(3, count($fields));
        $this->assertInstanceOf(Relation::class, $fields[2]);
        $this->assertEquals($fields[2]->name, 'comments');
        $this->assertEquals($fields[2]->fields, ['id', 'body']);
    }

    /** @test */
    public function it_parses_nested_relationships_fields()
    {
        $request = $this->buildRequest(['fields' => 'id,name,comments(id,author(id))']);

        $fields = (new RequestParser())->parseFields($request);

        $this->assertEquals(3, count($fields));
        $this->assertInstanceOf(Relation::class, $fields[2]);
        $this->assertInstanceOf(Relation::class, $fields[2]->fields[1]);
        $this->assertEquals('author', $fields[2]->fields[1]->name);
    }

    /** @test */
    public function it_supports_quoted_expressions_in_fields()
    {
        $request = $this->buildRequest(['fields' => 'id,"full,name",comments(id,"raw) (body")']);

        $fields = (new RequestParser())->parseFields($request);

        $this->assertEquals(3, count($fields));
        $this->assertEquals('full,name', $fields[1]);
        $this->assertEquals('raw) (body', $fields[2]->fields[1]);
    }
}
