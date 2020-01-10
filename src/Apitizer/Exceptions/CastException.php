<?php

namespace Apitizer\Exceptions;

/**
 * @see \Apitizer\Support\TypeCaster
 */
class CastException extends ApitizerException
{
    /**
     * @var mixed the value that failed to cast
     */
    public $value;

    /**
     * @var string
     */
    public $type;

    /**
     * @var null|string the format used when casting to date(time).
     */
    public $format;

    public function __construct($value, string $type, string $format = null)
    {
        parent::__construct();
        $this->value = $value;
        $this->type = $type;
        $this->format = $format;
    }
}
