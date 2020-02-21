<?php

namespace Apitizer\Validation;

use Tests\Unit\TestCase;
use Apitizer\Validation\Rules;

class RulesTest extends TestCase
{
    /** @test */
    public function it_defines_and_builds_rule_builders()
    {
        $rules = new Rules();
        $rules->define('store', function (RuleBuilder $builder) {
            $builder->string('name');
        });

        $storeRules = $rules->rules('store');

        $this->assertInstanceOf(RuleBuilder::class, $storeRules);
        $this->assertCount(1, $storeRules->fields());
    }

    /** @test */
    public function it_returns_an_empty_builder_when_none_is_defined()
    {
        $rules = new Rules();
        $builder = $rules->rules('store');

        $this->assertInstanceOf(RuleBuilder::class, $builder);
        $this->assertEmpty($builder->fields());
    }

    /** @test */
    public function it_resolves_all_builders_when_all_rules_are_requested()
    {
        $rules = new Rules();
        $rules->storeRules(function (RuleBuilder $builder) {});
        $rules->updateRules(function (RuleBuilder $builder) {});

        $ruleBuilders = $rules->rules();

        $this->assertCount(2, $ruleBuilders);
        foreach ($ruleBuilders as $builder) {
            $this->assertInstanceOf(RuleBuilder::class, $builder);
        }
    }
}
