<?php

namespace Tests\Feature\Support;

use Apitizer\Exceptions\DefinitionException;
use Apitizer\QueryBuilder;
use Apitizer\Support\SchemaValidator;
use Tests\Support\Builders\EmptyBuilder;
use Tests\Support\Builders\UserBuilder;
use Tests\Feature\TestCase;

class SchemaValidatorTest extends TestCase
{
    /** @test */
    public function it_validates_non_builder_classes_in_associations()
    {
        $this->assertHasErrors(new BuilderClassExpected());
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
    public function it_finds_multiple_errors_in_a_query_builder()
    {
        $this->assertHasErrors(new MultipleErrors(), 2);
    }

    private function assertHasErrors(QueryBuilder $queryBuilder, $count = 1)
    {
        $validator = (new SchemaValidator)->validate($queryBuilder);
        $this->assertTrue($validator->hasErrors());
        $this->assertCount($count, $validator->getErrors());
        foreach ($validator->getErrors() as $error) {
            $this->assertInstanceOf(DefinitionException::class, $error);
        }
    }
}

class NotABuilder
{
}
class BuilderClassExpected extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'author' => $this->association('user', NotABuilder::class),
        ];
    }
}

class FieldDefinitionExpected extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'id' => 1,
        ];
    }
}

class FilterDefinitionExpected extends EmptyBuilder
{
    public function filters(): array
    {
        return [
            'name' => 'not a filter',
        ];
    }
}

class SortDefinitionExpected extends EmptyBuilder
{
    public function sorts(): array
    {
        return [
            'name' => 'not a sort',
        ];
    }
}

class FilterHandlerNotDefined extends EmptyBuilder
{
    public function filters(): array
    {
        return [
            'name' => $this->filter(),
        ];
    }
}

class AssociationDoesNotExist extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'geckos' => $this->association('geckos', UserBuilder::class),
        ];
    }
}

class MultipleErrors extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'geckos' => $this->association('geckos', UserBuilder::class),
        ];
    }

    public function sorts(): array
    {
        return [
            'name' => 'not a sort',
        ];
    }
}
