<?php

namespace Apitizer\Transformers;

use DateTimeInterface;

class DateTimeFormat
{
    protected $format;

    public function __construct(string $format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    public function __invoke(DateTimeInterface $value)
    {
        return $value->format($this->format);
    }
}
