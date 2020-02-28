<?php

namespace Apitizer\Validation\Rules;

class AfterRule extends DateRule
{
    public function getName(): string
    {
        return 'after';
    }
}
