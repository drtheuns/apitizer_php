<?php

namespace Tests\Unit\Parser;

use Apitizer\Parser\InputParser;
use Apitizer\Parser\Relation;
use Apitizer\Parser\ParsedInput;
use Tests\Unit\TestCase;

class FieldParsingTest extends TestCase
{
    /** @test */
    public function it_parses_non_nested_fields()
    {
        $fields = $this->parse('id,name')->fields;

        $this->assertEquals(2, count($fields));
        $this->assertEquals($fields, ['id', 'name']);
    }

    /** @test */
    public function it_parses_relationships_fields()
    {
        $parsedInput = $this->parse('id,name,comments(id,body)');

        $this->assertEquals(2, count($parsedInput->fields));
        $this->assertEquals(1, count($parsedInput->associations));
        $this->assertInstanceOf(Relation::class, $parsedInput->associations[0]);
        $this->assertEquals($parsedInput->associations[0]->name, 'comments');
        $this->assertEquals($parsedInput->associations[0]->fields, ['id', 'body']);
    }

    /** @test */
    public function it_parses_nested_relationships_fields()
    {
        $parsedInput = $this->parse('id,name,comments(author(id), id)');

        $this->assertEquals(2, count($parsedInput->fields));
        $this->assertInstanceOf(Relation::class, $parsedInput->associations[0]);
        $this->assertInstanceOf(Relation::class, $parsedInput->associations[0]->associations[0]);
        $this->assertEquals('author', $parsedInput->associations[0]->associations[0]->name);
    }

    /** @test */
    public function it_supports_quoted_expressions_in_fields()
    {
        $parsedInput = $this->parse('id,"full,name",comments(id,"raw),-(body")');

        $this->assertEquals(2, count($parsedInput->fields));
        $this->assertEquals('full,name', $parsedInput->fields[1]);
        $this->assertEquals('raw),-(body', $parsedInput->associations[0]->fields[1]);
    }

    /** @test */
    public function white_space_characters_in_fields_are_ignored()
    {
        $parsedInput = $this->parse("id ,  name	,\r\ncomments(\u{200B} id , name )");
        $fields = $parsedInput->fields;
        $comments = $parsedInput->associations[0];

        $this->assertEquals(2, count($fields));
        $this->assertEquals('id', $fields[0]);
        $this->assertEquals('name', $fields[1]);
        $this->assertEquals('comments', $comments->name);
        $this->assertEquals('id', $comments->fields[0]);
        $this->assertEquals('name', $comments->fields[1]);
    }

    /** @test */
    public function white_space_in_quoted_expressions_are_kept()
    {
        $fields = $this->parse('"id, and	name", test')->fields;

        $this->assertEquals(2, count($fields));
        $this->assertEquals('id, and	name', $fields[0]);
    }

    /** @test */
    public function the_fields_may_be_an_array_of_strings()
    {
        $fields = $this->parse(['id', 'name'])->fields;
        $this->assertEquals(2, count($fields));
        $this->assertEquals(['id', 'name'], $fields);
    }

    /** @test */
    public function no_parsing_is_performed_when_an_array_is_passed()
    {
        $input = ['id', 'name', 'comments(id,name)', 'comments' => ['id', 'name']];
        $fields = $this->parse($input)->fields;

        $this->assertSame($input, $fields);
    }

    private function parse($fields): ParsedInput
    {
        $parsedInput = new ParsedInput;

        (new InputParser())->parseFields($parsedInput, $fields);

        return $parsedInput;
    }
}
