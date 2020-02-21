<?php

namespace Apitizer\Validation;

class DateTimeRuleBuilder extends DateRuleBuilder
{
    public function getType(): string
    {
        return 'datetime';
    }

    protected function getDefaultFormat()
    {
        return DATE_RFC3339;
    }
}
