<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\RuleBuilder;
use Illuminate\Support\Str;
use Tests\Support\TestValidationRules;

class TestCase extends \Tests\Unit\TestCase
{
    public function builder(): TestValidationRules
    {
        return new TestValidationRules();
    }

    protected function assertSimpleRule(string $type, string $rule)
    {
        $this->builder()
             ->rules(function (RuleBuilder $builder) use ($type, $rule) {
                 $builder->$type('name')->$rule();
             })
             ->assertRulesFor('name', [$type, Str::snake($rule)]);
    }
}
