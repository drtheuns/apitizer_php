<?php

namespace Apitizer\FieldTypes;

class AnyType extends BaseType
{
    public function type(): string
    {
        return 'any';
    }

    public function cast($value)
    {
        return $value;
    }
}
