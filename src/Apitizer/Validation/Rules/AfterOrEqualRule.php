<?php

namespace Apitizer\Validation\Rules;

class AfterOrEqualRule extends DateRule
{
    public function getName(): string
    {
        return 'after_or_equal';
    }
}
