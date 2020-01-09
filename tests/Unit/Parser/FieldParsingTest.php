<?php

namespace Tests\Unit\Parser;

use Apitizer\Parser\InputParser;
use Apitizer\Parser\Relation;
use Tests\Unit\TestCase;

class FieldParsingTest extends TestCase
{
    /** @test */
    public function it_parses_non_nested_fields()
    {
        $fields = $this->parse('id,name');

        $this->assertEquals(2, count($fields));
        $this->assertEquals($fields, ['id', 'name']);
    }

    /** @test */
    public function it_parses_relationships_fields()
    {
        $fields = $this->parse('id,name,comments(id,body)');

        $this->assertEquals(3, count($fields));
        $this->assertInstanceOf(Relation::class, $fields[2]);
        $this->assertEquals($fields[2]->name, 'comments');
        $this->assertEquals($fields[2]->fields, ['id', 'body']);
    }

    /** @test */
    public function it_parses_nested_relationships_fields()
    {
        $fields = $this->parse('id,name,comments(id,author(id))');

        $this->assertEquals(3, count($fields));
        $this->assertInstanceOf(Relation::class, $fields[2]);
        $this->assertInstanceOf(Relation::class, $fields[2]->fields[1]);
        $this->assertEquals('author', $fields[2]->fields[1]->name);
    }

    /** @test */
    public function it_supports_quoted_expressions_in_fields()
    {
        $fields = $this->parse('id,"full,name",comments(id,"raw),-(body")');

        $this->assertEquals(3, count($fields));
        $this->assertEquals('full,name', $fields[1]);
        $this->assertEquals('raw),-(body', $fields[2]->fields[1]);
    }

    /** @test */
    public function white_space_characters_in_fields_are_ignored()
    {
        $fields = $this->parse("id ,  name	,\r\ncomments(\u{200B} id , name )");

        $this->assertEquals(3, count($fields));
        $this->assertEquals('id', $fields[0]);
        $this->assertEquals('name', $fields[1]);
        $this->assertEquals('comments', $fields[2]->name);
        $this->assertEquals('id', $fields[2]->fields[0]);
        $this->assertEquals('name', $fields[2]->fields[1]);
    }

    /** @test */
    public function white_space_in_quoted_expressions_are_kept()
    {
        $fields = $this->parse('"id, and	name", test');

        $this->assertEquals(2, count($fields));
        $this->assertEquals('id, and	name', $fields[0]);
    }

    /** @test */
    public function the_fields_may_be_an_array_of_strings()
    {
        $fields = $this->parse(['id', 'name']);
        $this->assertEquals(2, count($fields));
        $this->assertEquals(['id', 'name'], $fields);
    }

    /** @test */
    public function no_parsing_is_performed_when_an_array_is_passed()
    {
        $input = ['id', 'name', 'comments(id,name)', 'comments' => ['id', 'name']];
        $fields = $this->parse($input);

        $this->assertSame($input, $fields);
    }

    private function parse($fields)
    {
        return (new InputParser())->parseFields($fields);
    }
}
