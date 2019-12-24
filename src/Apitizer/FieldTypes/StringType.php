<?php

namespace Apitizer\FieldTypes;

class StringType extends BaseType
{
    public function type(): string
    {
        return 'string';
    }

    public function cast($value)
    {
        return (string) $value;
    }
}
