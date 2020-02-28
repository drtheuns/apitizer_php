<?php

namespace Apitizer\Validation\Rules;

class ConfirmedRule extends FieldRule
{
    public function __construct(string $field)
    {
        $this->field = $field . '_confirmed';
    }

    public function getName(): string
    {
        return 'confirmed';
    }

    public function toValidationRule()
    {
        return $this->getName();
    }
}
