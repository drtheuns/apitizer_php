<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\DocumentableRule;
use Illuminate\Validation\Rules\NotIn;

class NotInRule extends NotIn implements DocumentableRule
{
    public function getDescription(): ?string
    {
        return trans('apitizer::validation.not_in', [
            'values' => implode(' or ', $this->values)
        ]);
    }
}
