<?php

namespace Apitizer\Validation\Rules;

class GteRule extends FieldRule
{
    public function getName(): string
    {
        return 'gte';
    }
}
