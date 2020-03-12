<?php

namespace Tests\Feature\Support;

use Apitizer\Exceptions\DefinitionException;
use Apitizer\Schema;
use Apitizer\Support\SchemaValidator;
use Tests\Support\Schemas\EmptySchema;
use Tests\Support\Schemas\UserSchema;
use Tests\Feature\TestCase;

class SchemaValidatorTest extends TestCase
{
    /** @test */
    public function it_validates_non_schema_classes_in_associations()
    {
        $this->assertHasErrors(new SchemaClassExpected());
    }

    /** @test */
    public function it_validates_that_associations_exist_on_the_model()
    {
        $this->assertHasErrors(new AssociationDoesNotExist());
    }

    /** @test */
    public function it_validates_that_fields_must_have_the_right_type()
    {
        $this->assertHasErrors(new FieldDefinitionExpected());
    }

    /** @test */
    public function it_validates_that_filters_must_have_the_right_type()
    {
        $this->assertHasErrors(new FilterDefinitionExpected());
    }

    /** @test */
    public function it_validates_that_filters_must_have_a_handler_defined()
    {
        $this->assertHasErrors(new FilterHandlerNotDefined());
    }

    /** @test */
    public function it_validates_that_sorts_must_have_the_right_type()
    {
        $this->assertHasErrors(new SortDefinitionExpected());
    }

    /** @test */
    public function it_finds_multiple_errors_in_a_schema()
    {
        $this->assertHasErrors(new MultipleErrors(), 2);
    }

    private function assertHasErrors(Schema $schema, $count = 1)
    {
        $validator = (new SchemaValidator)->validate($schema);
        $this->assertTrue($validator->hasErrors());
        $this->assertCount($count, $validator->getErrors());
        foreach ($validator->getErrors() as $error) {
            $this->assertInstanceOf(DefinitionException::class, $error);
        }
    }
}

class NotASchema
{
}
class SchemaClassExpected extends EmptySchema
{
    public function fields(): array
    {
        return [
            'author' => $this->association('user', NotASchema::class),
        ];
    }
}

class FieldDefinitionExpected extends EmptySchema
{
    public function fields(): array
    {
        return [
            'id' => 1,
        ];
    }
}

class FilterDefinitionExpected extends EmptySchema
{
    public function filters(): array
    {
        return [
            'name' => 'not a filter',
        ];
    }
}

class SortDefinitionExpected extends EmptySchema
{
    public function sorts(): array
    {
        return [
            'name' => 'not a sort',
        ];
    }
}

class FilterHandlerNotDefined extends EmptySchema
{
    public function filters(): array
    {
        return [
            'name' => $this->filter(),
        ];
    }
}

class AssociationDoesNotExist extends EmptySchema
{
    public function fields(): array
    {
        return [
            'geckos' => $this->association('geckos', UserSchema::class),
        ];
    }
}

class MultipleErrors extends EmptySchema
{
    public function fields(): array
    {
        return [
            'geckos' => $this->association('geckos', UserSchema::class),
        ];
    }

    public function sorts(): array
    {
        return [
            'name' => 'not a sort',
        ];
    }
}
