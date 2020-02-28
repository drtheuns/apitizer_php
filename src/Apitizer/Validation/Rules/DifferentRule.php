<?php

namespace Apitizer\Validation\Rules;

class DifferentRule extends FieldRule
{
    public function getName(): string
    {
        return 'different';
    }
}
