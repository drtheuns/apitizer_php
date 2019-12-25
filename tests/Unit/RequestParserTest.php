<?php

namespace Tests\Unit;

use Apitizer\RequestParser;
use Apitizer\Parser\Relation;
use Apitizer\Parser\Sort;

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
        $request = $this->buildRequest(['fields' => 'id,"full,name",comments(id,"raw),-(body")']);

        $fields = (new RequestParser())->parseFields($request);

        $this->assertEquals(3, count($fields));
        $this->assertEquals('full,name', $fields[1]);
        $this->assertEquals('raw),-(body', $fields[2]->fields[1]);
    }

    /** @test */
    public function it_can_parse_sorting_on_single_fields()
    {
        $request = $this->buildRequest(['sort' => 'name']);

        $sorts = (new RequestParser())->parseSorts($request);

        $this->assertEquals(1, count($sorts));
        $this->assertInstanceOf(Sort::class, $sorts[0]);
        $this->assertEquals('name', $sorts[0]->field);
        $this->assertEquals(Sort::ASC, $sorts[0]->order);
    }

    /** @test */
    public function it_can_parse_sorting_with_order_directions()
    {
        $request = $this->buildRequest(['sort' => 'name.desc']);

        $sorts = (new RequestParser())->parseSorts($request);

        $this->assertEquals(1, count($sorts));
        $this->assertInstanceOf(Sort::class, $sorts[0]);
        $this->assertEquals('name', $sorts[0]->field);
        $this->assertEquals(Sort::DESC, $sorts[0]->order);
    }

    /** @test */
    public function it_can_parse_multiple_comma_separated_sorts()
    {
        $request = $this->buildRequest(['sort' => 'name.desc,id.asc']);

        $sorts = (new RequestParser())->parseSorts($request);

        $this->assertEquals(2, count($sorts));

        $this->assertInstanceOf(Sort::class, $sorts[0]);
        $this->assertInstanceOf(Sort::class, $sorts[1]);

        $this->assertEquals('name', $sorts[0]->field);
        $this->assertEquals(Sort::DESC, $sorts[0]->order);

        $this->assertEquals('id', $sorts[1]->field);
        $this->assertEquals(Sort::ASC, $sorts[1]->order);
    }

    /** @test */
    public function it_can_parse_sorts_as_arrays()
    {
        $request = $this->buildRequest(['sort' => ['name.desc', 'id.asc']]);

        $sorts = (new RequestParser())->parseSorts($request);

        $this->assertEquals(2, count($sorts));

        $this->assertInstanceOf(Sort::class, $sorts[0]);
        $this->assertInstanceOf(Sort::class, $sorts[1]);

        $this->assertEquals('name', $sorts[0]->field);
        $this->assertEquals(Sort::DESC, $sorts[0]->order);

        $this->assertEquals('id', $sorts[1]->field);
        $this->assertEquals(Sort::ASC, $sorts[1]->order);
    }
}
