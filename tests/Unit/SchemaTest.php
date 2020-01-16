<?php

namespace Tests\Unit;

use Apitizer\Exceptions\SchemaDefinitionException;
use Apitizer\Schema;

class SchemaTest extends TestCase
{
    /** @test */
    public function it_rejects_non_query_building_classes()
    {
        $this->expectException(SchemaDefinitionException::class);

        new InvalidSchema;
    }
}

class InvalidSchema extends Schema
{
    protected function registerBuilders()
    {
        $this->register('NotAClass');
    }
}
