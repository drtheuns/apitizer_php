<?php

namespace Apitizer\Validation;

class IntegerRules extends NumberRules
{
    public function getType(): string
    {
        return 'integer';
    }

    public function getValidatableType()
    {
        return $this->getType();
    }
}
