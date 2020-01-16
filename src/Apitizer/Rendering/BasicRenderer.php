<?php

namespace Apitizer\Rendering;

use Apitizer\Policies\PolicyFailed;
use Apitizer\QueryBuilder;
use Illuminate\Support\Arr;

class BasicRenderer implements Renderer
{
    public function render(QueryBuilder $queryBuilder, $data, array $selectedFields): array
    {
        if ($this->isSingleRowOfData($data)) {
            return $this->renderOne($data, $selectedFields);
        }

        $result = [];

        foreach ($data as $row) {
            // When dealing with e.g. hasMany associations
            if ($row instanceof PolicyFailed) {
                continue;
            }

            $result[] = $this->renderOne($row, $selectedFields);
        }

        return $result;
    }

    protected function renderOne($row, array $selectedFields): array
    {
        $acc = [];

        foreach ($selectedFields as $fieldOrAssoc) {
            $renderedValue = $fieldOrAssoc->render($row, $this);

            // When a specific value fails, or when an association that returns
            // a single row fails (e.g. belongsTo)
            if ($renderedValue instanceof PolicyFailed) {
                continue;
            }

            $acc[$fieldOrAssoc->getName()] = $renderedValue;
        }

        return $acc;
    }

    /**
     * Check if we're dealing with a single row of data or a collection of rows.
     */
    protected function isSingleRowOfData($data): bool
    {
        return
            // Distinguish between arrays as lists of data, or arrays as maps.
            // Associative arrays (maps) are considered a single row of data.
            (is_array($data) && Arr::isAssoc($data))

            // Distinguish between e.g. Eloquent objects and Collection objects.
            // Non-iterable objects are considered a single row of data.
            || (is_object($data) && ! is_iterable($data));
    }
}
