<?php

namespace Apitizer\Types;

use Apitizer\Transformers\DateTimeFormat;

class DateTimeField extends Field implements FormattableField
{
    public function format(string $format = 'Y-m-d H:i:s'): Field
    {
        $this->transform(new DateTimeFormat($format));

        return $this;
    }
}
