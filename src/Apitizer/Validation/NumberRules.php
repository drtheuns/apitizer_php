<?php

namespace Apitizer\Validation;

use Apitizer\Validation\Rules\DigitsBetweenRule;
use Apitizer\Validation\Rules\DigitsRule;
use Apitizer\Validation\Rules\EndsWithRule;
use Apitizer\Validation\Rules\StartsWithRule;

class NumberRules extends FieldRuleBuilder
{
    use Concerns\SharedRules;

    public function digits(int $length): self
    {
        return $this->addRule(new DigitsRule($length));
    }

    public function digitsBetween(int $min, int $max): self
    {
        return $this->addRule(new DigitsBetweenRule($min, $max));
    }

    public function endsWith(array $values): self
    {
        return $this->addRule(new EndsWithRule($values));
    }

    public function startsWith(array $values): self
    {
        return $this->addRule(new StartsWithRule($values));
    }

    public function getType(): string
    {
        return 'number';
    }

    public function getValidatableType()
    {
        return 'numeric';
    }
}
