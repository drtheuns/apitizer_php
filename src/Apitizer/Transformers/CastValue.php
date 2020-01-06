<?php

namespace Apitizer\Transformers;

use Apitizer\Types\Field;
use Apitizer\Support\TypeCaster;

class CastValue
{
    protected $format;

    public function __construct(string $format = null)
    {
        $this->$format = $format;
    }

    public function __invoke($value, Field $field)
    {
        return TypeCaster::cast($value, $field->getType(), $this->format);
    }
}
