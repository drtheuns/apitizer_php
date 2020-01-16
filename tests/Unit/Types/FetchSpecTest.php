<?php

namespace Tests\Unit\Types;

use Apitizer\Types\FetchSpec;
use Apitizer\Types\Field;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;
use Tests\Support\Builders\EmptyBuilder;
use Tests\Unit\TestCase;

class FetchSpecTest extends TestCase
{
    /** @test */
    public function the_caller_can_check_if_a_field_was_requested()
    {
        $fetchSpec = new FetchSpec([$this->fieldWithName('name')]);
        $this->assertTrue($fetchSpec->fieldSelected('name'));

        $fetchSpec = new FetchSpec();
        $this->assertFalse($fetchSpec->fieldSelected('name'));
    }

    /** @test */
    public function the_caller_can_check_if_a_filter_was_requested()
    {
        $fetchSpec = new FetchSpec([], [], [$this->filterWithName('name')]);
        $this->assertTrue($fetchSpec->filterSelected('name'));

        $fetchSpec = new FetchSpec();
        $this->assertFalse($fetchSpec->filterSelected('name'));
    }

    /** @test */
    public function the_caller_can_check_if_specific_sorting_was_requested()
    {
        $fetchSpec = new FetchSpec([], [$this->sortWithName('name')]);
        $this->assertTrue($fetchSpec->sortSelected('name'));

        $fetchSpec = new FetchSpec();
        $this->assertFalse($fetchSpec->sortSelected('name'));
    }

    private function fieldWithName(string $name)
    {
        return (new Field(new EmptyBuilder, $name, 'string'))->setName($name);
    }

    private function filterWithName(string $name)
    {
        return (new Filter(new EmptyBuilder))->setName($name);
    }

    private function sortWithName(string $name)
    {
        return (new Sort(new EmptyBuilder))->setName($name);
    }
}
