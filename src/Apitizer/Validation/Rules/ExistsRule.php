<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;
use Illuminate\Validation\Rules\Exists;

class ExistsRule extends Exists implements ValidationRule
{
    public function getName(): string
    {
        return 'exists';
    }

    public function getParameters(): array
    {
        return [
            'table' => $this->table,
            'column' => $this->column,
        ];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.exists');
    }

    public function toValidationRule()
    {
        return $this;
    }

    public function toHtml()
    {
        return $this->getDocumentation();
    }
}
