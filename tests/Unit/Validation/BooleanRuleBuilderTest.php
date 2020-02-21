<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\RuleBuilder;

class BooleanRuleBuilderTest extends TestCase
{
    /** @test */
    public function it_always_validates_the_type()
    {
        $this->builder()
             ->rules(function (RuleBuilder $builder) {
                 $builder->boolean('name');
             })
             ->assertRulesFor('name', ['boolean']);
    }

    /** @test */
    public function it_validates_simple_rules()
    {
        $this->simpleRule('accepted');
    }

    protected function simpleRule(string $rule)
    {
        parent::assertSimpleRule('boolean', $rule);
    }
}
