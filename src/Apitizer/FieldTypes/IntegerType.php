<?php

namespace Apitizer\FieldTypes;

class IntegerType extends BaseType
{
    public function type(): string
    {
        return 'integer';
    }

    public function cast($value)
    {
        return (int) $value;
    }
}
