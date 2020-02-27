<?php

namespace Apitizer\Validation\Concerns;

use Apitizer\Validation\Rules\BetweenRule;
use Apitizer\Validation\Rules\ConfirmedRule;
use Apitizer\Validation\Rules\DifferentRule;
use Apitizer\Validation\Rules\ExistsRule;
use Apitizer\Validation\Rules\GteRule;
use Apitizer\Validation\Rules\GtRule;
use Apitizer\Validation\Rules\InArrayRule;
use Apitizer\Validation\Rules\InRule;
use Apitizer\Validation\Rules\LteRule;
use Apitizer\Validation\Rules\LtRule;
use Apitizer\Validation\Rules\MaxRule;
use Apitizer\Validation\Rules\MinRule;
use Apitizer\Validation\Rules\NotInRule;
use Apitizer\Validation\Rules\NotRegexRule;
use Apitizer\Validation\Rules\RegexRule;
use Apitizer\Validation\Rules\RequiredIfRule;
use Apitizer\Validation\Rules\RequiredWithAllRule;
use Apitizer\Validation\Rules\RequiredWithoutAllRule;
use Apitizer\Validation\Rules\RequiredWithoutRule;
use Apitizer\Validation\Rules\RequiredWithRule;
use Apitizer\Validation\Rules\SameRule;
use Apitizer\Validation\Rules\SizeRule;
use Apitizer\Validation\Rules\UniqueRule;

/**
 * This is a trait, rather than a bunch of method on the FieldRuleBuilder class
 * because it allows for better autocomplete with IDEs due to the "self" return
 * type being interpreted as "FieldRuleBuilder", rather than a covariant type.
 */
trait SharedRules
{
    public function required(): self
    {
        $this->required = true;

        return $this;
    }

    public function requiredIf(RequiredIfRule $rule): self
    {
        return $this->addRule($rule);
    }

    public function requiredWith(array $fields): self
    {
        return $this->addRule(new RequiredWithRule($fields));
    }

    public function requiredWithAll(array $fields): self
    {
        return $this->addRule(new RequiredWithAllRule($fields));
    }

    public function requiredWithout(array $fields): self
    {
        return $this->addRule(new RequiredWithoutRule($fields));
    }

    public function requiredWithoutAll(array $fields): self
    {
        return $this->addRule(new RequiredWithoutAllRule($fields));
    }

    public function nullable(): self
    {
        $this->nullable = true;

        return $this;
    }

    public function size($size): self
    {
        return $this->addRule(new SizeRule($size, $this->getType()));
    }

    public function max($size): self
    {
        return $this->addRule(new MaxRule($size, $this->getType()));
    }

    public function min($size): self
    {
        return $this->addRule(new MinRule($size, $this->getType()));
    }

    public function bail(): self
    {
        // Doesn't need to show up in the documentation.
        return $this->addRule('bail');
    }

    public function confirmed(): self
    {
        return $this->addRule(new ConfirmedRule($this->getFieldName()));
    }

    public function between(int $min, int $max): self
    {
        return $this->addRule(new BetweenRule($min, $max));
    }

    public function different(string $field): self
    {
        return $this->addRule(new DifferentRule($field));
    }

    public function same(string $field): self
    {
        return $this->addRule(new SameRule($field));
    }

    public function regex(string $regex): self
    {
        return $this->addRule(new RegexRule($regex));
    }

    public function notRegex(string $regex): self
    {
        return $this->addRule(new NotRegexRule($regex));
    }

    public function gt(string $field): self
    {
        return $this->addRule(new GtRule($field));
    }

    public function gte(string $field): self
    {
        return $this->addRule(new GteRule($field));
    }

    public function lt(string $field): self
    {
        return $this->addRule(new LtRule($field));
    }

    public function lte(string $field): self
    {
        return $this->addRule(new LteRule($field));
    }

    public function in(array $values): self
    {
        return $this->addRule(new InRule($values));
    }

    public function notIn(array $values): self
    {
        return $this->addRule(new NotInRule($values));
    }

    public function inArray(string $field): self
    {
        return $this->addRule(new InArrayRule($field));
    }

    public function filled(): self
    {
        return $this->addConstraint('filled');
    }

    public function exists(string $table, string $column = null): self
    {
        return $this->addRule(new ExistsRule($table, $column));
    }

    public function unique(string $table, string $column = null): self
    {
        return $this->addRule(new UniqueRule($table, $column));
    }

    public function sometimes(): self
    {
        return $this->addConstraint('sometimes');
    }

    public function present(): self
    {
        return $this->addConstraint('present');
    }
}
