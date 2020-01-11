<?php

namespace Apitizer\Types\Concerns;

use ArrayAccess;

trait FetchesValueFromRow
{
    protected function valueFromRow($row, string $key)
    {
        $value = null;

        if ($row instanceof ArrayAccess || is_array($row)) {
            $value = $row[$key];
        } else if (is_object($row)) {
            $value = $row->{$key};
        }

        return $value;
    }
}
