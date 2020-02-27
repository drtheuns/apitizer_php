<?php

namespace Apitizer\Validation\Rules;

class EndsWithRule extends StartsWithRule
{
    public function getName(): string
    {
        return 'ends_with';
    }
}
