<?php

namespace Apitizer\Validation\Rules;

class RequiredWithAllRule extends RequiredWithRule
{
    public function getName(): string
    {
        return 'required_with_all';
    }
}
