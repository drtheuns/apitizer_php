<?php

namespace Apitizer\Validation\Rules;

class BeforeOrEqualRule extends DateRule
{
    public function getName(): string
    {
        return 'before_or_equal';
    }
}
