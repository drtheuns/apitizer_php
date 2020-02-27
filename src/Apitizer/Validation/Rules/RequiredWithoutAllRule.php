<?php

namespace Apitizer\Validation\Rules;

class RequiredWithoutAllRule extends RequiredWithRule
{
    public function getName(): string
    {
        return 'required_without_all';
    }
}
