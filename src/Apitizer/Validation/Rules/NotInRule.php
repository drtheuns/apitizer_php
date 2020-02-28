<?php

namespace Apitizer\Validation\Rules;

class NotInRule extends InRule
{
    public function getName(): string
    {
        return 'not_in';
    }
}
