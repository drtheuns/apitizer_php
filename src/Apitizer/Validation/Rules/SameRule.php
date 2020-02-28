<?php

namespace Apitizer\Validation\Rules;

class SameRule extends FieldRule
{
    public function getName(): string
    {
        return 'same';
    }
}
