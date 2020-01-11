<?php

namespace Apitizer\Rendering;

use Apitizer\Policies\PolicyFailed;
use Apitizer\QueryBuilder;

class BasicRenderer implements Renderer
{
    public function render(
        QueryBuilder $queryBuilder,
        $data,
        array $selectedFields
    ): array
    {
        // Check if we're dealing with a single row of data.
        if ($this->isSingleDataModel($data) || $this->isNonCollectionObject($data)) {
            return $this->renderOne($data, $selectedFields);
        }

        $result = [];

        foreach ($data as $row) {
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

            if ($renderedValue instanceof PolicyFailed) {
                continue;
            }

            $acc[$fieldOrAssoc->getName()] = $renderedValue;
        }

        return $acc;
    }

    protected function isSingleDataModel($data): bool
    {
        // Distinguish between arrays as list and arrays as maps.
        return is_array($data) && $this->isAssoc($data);
    }

    protected function isNonCollectionObject($data): bool
    {
        // Distinguish between e.g. Eloquent objects and Collection objects.
        return is_object($data) && !is_iterable($data);
    }

    private function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }
}
