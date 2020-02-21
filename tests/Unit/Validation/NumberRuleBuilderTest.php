<?php

namespace Tests\Unit\Validation;

class NumberRuleBuilderTest extends TestCase
{
    /** @test */
    public function it_always_validates_the_type()
    {
        $this->builder()
             ->rules(function ($builder) {
                 $builder->integer('e1');
                 $builder->number('e2');
             })
             ->assertRulesFor('e1', ['integer'])
             ->assertRulesFor('e2', ['numeric']);
    }

    /** @test */
    public function it_validates_ends_with()
    {
        $this->builder()
            ->rules(function ($builder) {
                $builder->number('e1')->endsWith('name');
                $builder->number('e2')->endsWith(['name', 'wow']);
            })
            ->assertRulesFor('e1', ['numeric', 'ends_with:name'])
            ->assertRulesFor('e2', ['numeric', 'ends_with:name,wow']);
    }

    /** @test */
    public function it_validates_starts_with()
    {
        $this->builder()
            ->rules(function ($builder) {
                $builder->number('e1')->startsWith('name');
                $builder->number('e2')->startsWith(['name', 'wow']);
            })
            ->assertRulesFor('e1', ['numeric', 'starts_with:name'])
            ->assertRulesFor('e2', ['numeric', 'starts_with:name,wow']);
    }

    /** @test */
    public function it_validates_digits()
    {
        $this->builder()
            ->rules(function ($builder) {
                $builder->number('name')->digits(5);
            })
            ->assertRulesFor('name', ['numeric', 'digits:5']);
    }

    /** @test */
    public function it_validates_digits_between()
    {
        $this->builder()
            ->rules(function ($builder) {
                $builder->number('name')->digitsBetween(4, 12);
            })
            ->assertRulesFor('name', ['numeric', 'digits_between:4,12']);
    }
}
