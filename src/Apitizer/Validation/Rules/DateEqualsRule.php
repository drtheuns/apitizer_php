<?php

namespace Apitizer\Validation\Rules;

class DateEqualsRule extends DateRule
{
    public function getName(): string
    {
        return 'date_equals';
    }
}
