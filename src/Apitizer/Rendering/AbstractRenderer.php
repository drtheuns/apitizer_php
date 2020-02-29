<?php

namespace Apitizer\Rendering;

use Apitizer\Types\Concerns\FetchesValueFromRow;
use Illuminate\Support\Arr;

abstract class AbstractRenderer
{
    use FetchesValueFromRow;

    /**
     * Check if we're dealing with a single row of data or a collection of rows.
     *
     * @param array<mixed>|object|iterable<mixed>|mixed $data
     */
    protected function isSingleRowOfData($data): bool
    {
        return
            // Distinguish between arrays as lists of data, or arrays as maps.
            // Associative arrays (maps) are considered a single row of data.
            (is_array($data) && Arr::isAssoc($data))

            // Distinguish between e.g. Eloquent objects and Collection objects.
            // Non-iterable objects are considered a single row of data.
            || (is_object($data) && !is_iterable($data));
    }
}
