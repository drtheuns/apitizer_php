<?php

namespace Apitizer\Validation\Rules;

class NotRegexRule extends RegexRule
{
    public function getName(): string
    {
        return 'not_regex';
    }
}
