<?php

namespace Apitizer\Validation\Rules;

class LteRule extends FieldRule
{
    public function getName(): string
    {
        return 'lte';
    }
}
