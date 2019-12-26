<?php

namespace Apitizer\Transformers;

use Apitizer\Types\Field;
use Apitizer\Support\TypeCaster;
use DateTimeInterface;

class CastValue
{
    public function __invoke($value, Field $field)
    {
        return TypeCaster::cast($value, $field->getType());
    }
}
