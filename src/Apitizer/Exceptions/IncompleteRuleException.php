<?php

namespace Apitizer\Exceptions;

class IncompleteRuleException extends ApitizerException
{
    static function arrayTypeExpected()
    {
        return new static("Expected a type definition after the array");
    }
}
