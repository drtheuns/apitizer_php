<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\RuleBuilder;

class ArrayRuleBuilderTest extends TestCase
{
    /** @test */
    public function it_always_validates_the_type()
    {
        $this->builder()
             ->rules(function (RuleBuilder $builder) {
                 $builder->array('name');
             })
             ->assertRulesFor('name', ['array']);
    }

    /** @test */
    public function it_validates_simple_rules()
    {
        $this->simpleRule('distinct');
    }

    private function simpleRule(string $rule)
    {
        $this->assertSimpleRule('array', $rule);
    }
}
