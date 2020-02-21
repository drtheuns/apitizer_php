<?php

namespace Apitizer\Validation;

class IntegerRuleBuilder extends NumberRuleBuilder
{
    public function getType(): string
    {
        return 'integer';
    }

    protected function getTypeRule()
    {
        return 'integer';
    }
}
