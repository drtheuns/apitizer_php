<?php

namespace Apitizer\Transformers;

use Apitizer\Types\AbstractField;
use Apitizer\Support\TypeCaster;

class CastValue
{
    /**
     * @var string|null
     */
    protected $format;

    public function __construct(string $format = null)
    {
        $this->$format = $format;
    }

    /**
     * @param mixed $value
     * @param mixed $row
     * @param AbstractField $field
     *
     * @return mixed
     */
    public function __invoke($value, $row, AbstractField $field)
    {
        return TypeCaster::cast($value, $field->getType(), $this->format);
    }
}
