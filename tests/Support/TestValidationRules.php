<?php

namespace Tests\Support;

use Apitizer\Validation\RuleBuilder;
use PHPUnit\Framework\Assert as PHPUnit;
use Closure;

class TestValidationRules
{
    /**
     * @var RuleBuilder
     */
    protected $builder;

    /**
     * @var array
     */
    protected $validationRules;

    public function __construct()
    {
        $this->builder = new RuleBuilder;
    }

    public function rules(Closure $callback): self
    {
        $callback($this->builder);

        return $this;
    }

    /**
     * Assert that the generated rules are equal to the given rules.
     */
    public function assertRules(array $rules): self
    {
        $this->ensureValidationRules();

        PHPUnit::assertEquals(
            $rules,
            $this->validationRules,
            'The generated rules did not match the expected rules',
        );

        return $this;
    }

    /**
     * Assert that the rules for some field are equal to the expected rules.
     */
    public function assertRulesFor(string $field, array $rules): self
    {
        $this->ensureValidationRules();

        if (! isset($this->validationRules[$field])) {
            PHPUnit::fail("No rules have been defined for field [$field]");
        }

        PHPUnit::assertEquals(
            $rules,
            $this->validationRules[$field],
            "The rules for [$field] did not match the expected rules"
        );

        return $this;
    }

    public function assertRulesSubset(array $rules): self
    {
        $this->ensureValidationRules();

        PHPUnit::assertArraySubset($rules, $this->validationRules);

        return $this;
    }

    private function ensureValidationRules()
    {
        // They have already been loaded.
        if (! is_null($this->validationRules)) {
            return;
        }

        $this->validationRules = $this->builder->toValidationRules();
    }
}
