<?php

namespace Apitizer\Validation;

class BooleanRules extends FieldRuleBuilder
{
    use Concerns\SharedRules;

    public function accepted(): self
    {
        return $this->addConstraint('accepted');
    }

    public function getType(): string
    {
        return 'boolean';
    }
}
