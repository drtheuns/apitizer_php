<?php

namespace Apitizer\Rendering;

use Apitizer\QueryBuilder;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Types\Concerns\FetchesValueFromRow;
use Apitizer\Policies\PolicyFailed;
use Illuminate\Support\Arr;

abstract class AbstractRenderer
{
    use FetchesValueFromRow;

    /**
     * @param QueryBuilder $queryBuilder
     * @param mixed $data
     * @param AbstractField[] $fields
     * @param Association[] $associations
     *
     * @return array<string, mixed>|array<int, array<string, mixed>>
     */
    public function doRender(
        QueryBuilder $queryBuilder,
        $data,
        array $fields,
        array $associations
    ): array {
        if ($this->isSingleRowOfData($data)) {
            return $this->renderSingleRow($data, $queryBuilder, $fields, $associations);
        }
        return $this->renderMany($data, $queryBuilder, $fields, $associations);
    }

    /**
     * @param mixed $data
     * @param QueryBuilder $queryBuilder
     * @param AbstractField[] $fields
     * @param Association[] $associations
     *
     * @return array<int, array<string, mixed>>
     */
    public function renderMany(
        $data,
        QueryBuilder $queryBuilder,
        array $fields,
        array $associations
    ): array {
        return collect($data)->map(function ($row) use ($queryBuilder, $fields, $associations) {
            return $this->renderSingleRow($row, $queryBuilder, $fields, $associations);
        })->all();
    }

    /**
     * @param mixed $row
     * @param QueryBuilder $queryBuilder
     * @param AbstractField[] $fields
     * @param Association[] $associations
     *
     * @return array<string, mixed>
     */
    abstract protected function renderSingleRow(
        $row,
        QueryBuilder $queryBuilder,
        array $fields,
        array $associations
    ): array ;

    /**
     * @param mixed $row
     * @param AbstractField $field
     * @param array<string, mixed> $renderedData
     *
     * @throws InvalidOutputException if the value does not adhere to the
     *         requirements set by the field. For example, if the field is not
     *         nullable but the value is null, this will throw an error. Enum
     *         field may also throw an error if the value is not in the enum.
     */
    protected function addRenderedField(
        $row,
        AbstractField $field,
        array &$renderedData
    ): void {
        $value = $field->render($row, $this);

        if ($value instanceof PolicyFailed) {
            return;
        }

        $renderedData[$field->getName()] = $value;
    }

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