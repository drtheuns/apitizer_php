<?php

namespace Apitizer\Validation;

class NumberRuleBuilder extends TypedRuleBuilder
{
    /**
     * Validate that the value has exactly $length digits.
     */
    public function digits(int $length): self
    {
        return $this->digitsRule($length);
    }

    /**
     * Validate that the value has between $min and $max digits.
     */
    public function digitsBetween(int $min, int $max): self
    {
        return $this->digitsBetweenRule($min, $max);
    }

    /**
     * Validate that the value ends with one of the given values.
     *
     * @param array|string $values
     *
     * @see \Illuminate\Support\Str::endsWith
     */
    public function endsWith($values): self
    {
        return $this->endsWithRule($values);
    }

    /**
     * Validate that the value starts with one of the given values.
     *
     * @param array|string $values
     *
     * @see \Illuminate\Support\Str::startsWith
     */
    public function startsWith($values): self
    {
        return $this->startsWithRule($values);
    }

    public function getType(): string
    {
        return 'number';
    }

    protected function getTypeRule()
    {
        return 'numeric';
    }
}
