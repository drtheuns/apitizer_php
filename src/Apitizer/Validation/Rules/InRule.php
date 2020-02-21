<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\DocumentableRule;
use Illuminate\Validation\Rules\In;

class InRule extends In implements DocumentableRule
{
    public function getDescription(): ?string
    {
        return trans('apitizer::validation.in', [
            'values' => implode(' or ', $this->values)
        ]);
    }
}
