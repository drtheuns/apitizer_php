<?php

namespace Apitizer\Validation;

class ArrayRuleBuilder extends TypedRuleBuilder
{
    public function distinct(): self
    {
        return $this->addSimpleRule('distinct');
    }

    public function getType(): string
    {
        return 'array';
    }
}
