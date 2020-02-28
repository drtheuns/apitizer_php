<?php

namespace Apitizer\Types\Concerns;

use ArrayAccess;
use Illuminate\Database\Eloquent\Model;

trait FetchesValueFromRow
{
    /**
     * @param array|Model|mixed $row
     * @param string $key
     *
     * @return mixed
     */
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
