<?php

namespace Apitizer\Validation\Rules;

class LtRule extends FieldRule
{
    public function getName(): string
    {
        return 'lt';
    }
}
