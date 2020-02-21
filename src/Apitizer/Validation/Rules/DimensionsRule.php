<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\DocumentableRule;
use Apitizer\Validation\ValidationRule;
use Illuminate\Validation\Rules\Dimensions;

class DimensionsRule extends Dimensions implements DocumentableRule, ValidationRule
{
    public function toValidationRule()
    {
        return $this->__toString();
    }

    public function getRule(): string
    {
        return 'dimensions';
    }

    public function getParameters()
    {
        return $this->constraints;
    }

    public function getDescription(): ?string
    {
        $constraints = [];

        foreach ($this->constraints as $key => $constraint) {
            $constraints[] = "$key: $constraint";
        }

        $constraint = implode(', ', $constraints);

        return trans('apitizer::validation.dimensions', ['constraints' => $constraints]);
    }
}
