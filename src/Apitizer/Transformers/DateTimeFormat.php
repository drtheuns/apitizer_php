<?php

namespace Apitizer\Transformers;

use DateTimeInterface;

class DateTimeFormat
{
    /**
     * @var string
     */
    protected $format;

    public function __construct(string $format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    public function __invoke(DateTimeInterface $value): string
    {
        return $value->format($this->format);
    }
}
