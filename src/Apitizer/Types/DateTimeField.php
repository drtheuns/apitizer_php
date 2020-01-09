<?php

namespace Apitizer\Types;

use Apitizer\Transformers\DateTimeFormat;

class DateTimeField extends Field
{
    /**
     * @return $this
     */
    public function format(string $format = null): DateTimeField
    {
        $format = $format ?? ($this->type == 'date' ? 'Y-m-d' : 'Y-m-d H:i:s');

        $this->transform(new DateTimeFormat($format));

        return $this;
    }
}
