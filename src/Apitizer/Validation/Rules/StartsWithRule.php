<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class StartsWithRule implements ValidationRule
{
    protected $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getName(): string
    {
        return 'starts_with';
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
        return $this->getName() . ':' . implode(',', $this->values);
    }

    public function toHtml()
    {
        $values = collect($this->values)->map(function ($value) {
            return '<code>' . $value . '</code>';
        })->implode(', ');

        return trans("apitizer::validation.{$this->getName()}") . ': ' . $values;
    }
}
