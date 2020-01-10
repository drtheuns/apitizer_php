<?php

namespace Apitizer\Types;

use ArrayAccess;

trait RendersValues
{
    protected function valueFromRow($row, string $key)
    {
        $value = null;

        if ($row instanceof ArrayAccess || is_array($row)) {
            $value = $row[$this->getKey()];
        } else if (is_object($row)) {
            $value = $row->{$this->getKey()};
        }

        return $value;
    }
}
