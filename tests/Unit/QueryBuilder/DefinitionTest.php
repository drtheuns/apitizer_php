<?php

namespace Tests\Unit\QueryBuilder;

use Apitizer\Exceptions\DefinitionException;
use Apitizer\Types\Field;
use Tests\Unit\TestCase;
use Tests\Feature\Builders\EmptyBuilder;

class DefinitionTest extends TestCase
{
    // The bindings are needed for this test.
    protected function getPackageProviders($app)
    {
        return ['Apitizer\ServiceProvider'];
    }

    /** @test */
    public function it_casts_string_fields_to_any_type()
    {
        $fields = (new AnyBuilder())->getFields();

        $this->assertCount(1, $fields);
        $this->assertInstanceOf(Field::class, $fields['any']);
        $this->assertEquals('any', $fields['any']->getType());
    }

    /** @test */
    public function it_throws_an_exception_when_the_filter_is_not_a_filter_instance()
    {
        $this->expectException(DefinitionException::class);
        (new InvalidFilter())->getFilters();
    }

    /** @test */
    public function it_throws_an_exception_when_a_field_has_an_invalid_type()
    {
        $this->expectException(DefinitionException::class);
        (new InvalidField())->getFields();
    }

    /** @test */
    public function it_throws_an_exception_when_an_unexpected_sorting_type_is_given()
    {
        $this->expectException(DefinitionException::class);
        (new InvalidSort())->getSorts();
    }

    /** @test */
    public function it_throws_an_exception_when_an_association_uses_a_non_builder_class()
    {
        $this->expectException(DefinitionException::class);
        (new InvalidAssoc())->getFields();
    }

    /** @test */
    public function it_throws_an_exception_if_the_association_does_not_exist_on_the_model()
    {
        $this->expectException(DefinitionException::class);
        (new AssocDoesNotExist())->getFields();
    }
}


class InvalidFilter extends EmptyBuilder
{
    public function filters(): array
    {
        return [
            'error' => 'unexpected type',
        ];
    }
}

class InvalidField extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'error' => null,
        ];
    }
}

class InvalidSort extends EmptyBuilder
{
    public function sorts(): array
    {
        return [
            'error' => 'expected filter instance',
        ];
    }
}

class NotABuilder {}

class InvalidAssoc extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'error' => $this->association('error', NotABuilder::class),
        ];
    }
}

class AssocDoesNotExist extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'error' => $this->association('assoc_not_on_model', InvalidAssoc::class),
        ];
    }
}

class AnyBuilder extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'any' => 'any',
        ];
    }
}

class FloatBuilder extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'float' => $this->float('prices_should_not_use_floats'),
        ];
    }
}
