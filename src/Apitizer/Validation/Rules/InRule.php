<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;
use Illuminate\Validation\Rules\In;

class InRule extends In implements ValidationRule
{
    public function getName(): string
    {
        return 'in';
    }

    public function getParameters(): array
    {
        return [
            'values' => $this->values,
        ];
    }

    public function getDocumentation(): ?string
    {
        return trans("apitizer::validation.{$this->getName()}");
    }

    public function toValidationRule()
    {
        return (string) $this;
    }

    public function toHtml()
    {
        $values = collect($this->values)->map(function ($value) {
            return '<code>' . $value . '</code>';
        })->implode(', ');

        return $this->getDocumentation() . ': ' . $values;
    }
}
