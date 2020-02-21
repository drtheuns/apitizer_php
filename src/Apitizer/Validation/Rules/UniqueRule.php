<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\DocumentableRule;
use Illuminate\Validation\Rules\Unique;

class UniqueRule extends Unique implements DocumentableRule
{
    public function getDescription(): ?string
    {
        return trans('apitizer::validation.unique');
    }
}
