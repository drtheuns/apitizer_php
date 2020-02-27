<?php

namespace Apitizer\Validation\Rules;

class InArrayRule extends FieldRule
{
    public function __construct(string $field)
    {
        $this->field = rtrim($field, '.*');
    }

    public function getName(): string
    {
        return 'in_array';
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . $this->field . '.*';
    }
}
