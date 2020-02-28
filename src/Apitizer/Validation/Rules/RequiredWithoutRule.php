<?php

namespace Apitizer\Validation\Rules;

class RequiredWithoutRule extends RequiredWithRule
{
    public function getName(): string
    {
        return 'required_without';
    }
}
