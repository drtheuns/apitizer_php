<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;
use Illuminate\Validation\Rules\Unique;

class UniqueRule extends Unique implements ValidationRule
{
    public function getName(): string
    {
        return 'unique';
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
        return trans('apitizer::validation.unique');
    }

    public function toValidationRule()
    {
        return $this;
    }

    public function toHtml(): string
    {
        return $this->getDocumentation() ?? '';
    }
}
