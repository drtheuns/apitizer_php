<?php

namespace Apitizer\Validation\Rules;

class BeforeRule extends DateRule
{
    public function getName(): string
    {
        return 'before';
    }
}
