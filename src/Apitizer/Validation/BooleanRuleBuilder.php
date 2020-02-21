<?php

namespace Apitizer\Validation;

class BooleanRuleBuilder extends TypedRuleBuilder
{
    public function accepted(): self
    {
        return $this->addSimpleRule('accepted');
    }

    public function getType(): string
    {
        return 'boolean';
    }
}
