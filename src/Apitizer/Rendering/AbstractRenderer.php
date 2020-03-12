<?php

namespace Apitizer\Rendering;

use Apitizer\Schema;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Types\Concerns\FetchesValueFromRow;
use Apitizer\Policies\PolicyFailed;
use Apitizer\Types\FetchSpec;
use Apitizer\Apitizer;
use Apitizer\Exceptions\InvalidOutputException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

abstract class AbstractRenderer implements Renderer
{
    use FetchesValueFromRow;

    public function paginate(Schema $schema, LengthAwarePaginator $paginator, FetchSpec $fetchSpec)
    {
        $renderedData = $this->render($schema, $paginator->getCollection(), $fetchSpec);

        $paginator->setCollection(collect($renderedData));

        /** @var array<string, mixed> $queryParameters */
        $queryParameters = $schema->getRequest()->query();

        // Ensure the all the supported query parameters that were passed in are
        // also present in the pagination links.
        $queryParameters = Arr::only(
            $queryParameters,
            array_values(Apitizer::getQueryParams())
        );
        $paginator->appends($queryParameters);

        return $paginator;
    }

    /**
     * @param Schema $schema
     * @param mixed $data
     * @param AbstractField[] $fields
     * @param Association[] $associations
     *
     * @return array<string, mixed>|array<int, array<string, mixed>>
     */
    public function doRender(
        Schema $schema,
        $data,
        array $fields,
        array $associations
    ): array {
        if ($this->isSingleRowOfData($data)) {
            return $this->renderSingleRow($data, $schema, $fields, $associations);
        }
        return $this->renderMany($data, $schema, $fields, $associations);
    }

    /**
     * @param mixed $data
     * @param Schema $schema
     * @param AbstractField[] $fields
     * @param Association[] $associations
     *
     * @return array<int, array<string, mixed>>
     */
    public function renderMany(
        $data,
        Schema $schema,
        array $fields,
        array $associations
    ): array {
        return collect($data)->map(function ($row) use ($schema, $fields, $associations) {
            return $this->renderSingleRow($row, $schema, $fields, $associations);
        })->all();
    }

    /**
     * @param mixed $row
     * @param Schema $schema
     * @param AbstractField[] $fields
     * @param Association[] $associations
     *
     * @return array<string, mixed>
     */
    abstract protected function renderSingleRow(
        $row,
        Schema $schema,
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
