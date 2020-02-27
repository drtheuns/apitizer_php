<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;
use Illuminate\Validation\Rules\Dimensions;

class DimensionsRule extends Dimensions implements ValidationRule
{
    public function getName(): string
    {
        return 'dimensions';
    }

    public function getParameters(): array
    {
        return $this->constraints;
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.dimensions');
    }

    public function toValidationRule()
    {
        return $this;
    }

    public function toHtml()
    {
        $list = collect($this->constraints)->map(function ($constraint, $key) {
            return "<li>$key: $constraint</li>";
        })->implode("\n");

        return trans('apitizer::validation.dimensions') . "<ul>$list</ul>";
    }
}
