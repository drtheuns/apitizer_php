<?php

namespace Apitizer\Validation\Rules;

class GtRule extends FieldRule
{
    public function getName(): string
    {
        return 'gt';
    }
}
