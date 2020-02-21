<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\DocumentableRule;
use Illuminate\Validation\Rules\Exists;

class ExistsRule extends Exists implements DocumentableRule
{
    public function getDescription(): ?string
    {
        return trans('apitizer::validation.exists');
    }
}
