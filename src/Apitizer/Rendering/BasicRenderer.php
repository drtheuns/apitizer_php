<?php

namespace Apitizer\Rendering;

use Apitizer\Policies\PolicyFailed;
use Apitizer\QueryBuilder;
use Apitizer\Types\FetchSpec;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Exceptions\InvalidOutputException;

class BasicRenderer extends AbstractRenderer implements Renderer
{
    public function render(QueryBuilder $queryBuilder, $data, FetchSpec $fetchSpec): array
    {
        return $this->doRender(
            $queryBuilder, $data,
            $fetchSpec->getFields(),
            $fetchSpec->getAssociations()
        );
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param mixed $data
     * @param AbstractField[] $fields
     * @param Association[] $associations
     *
     * @return array<string, mixed>|array<int, array<string, mixed>>
     */
    protected function doRender(
        QueryBuilder $queryBuilder,
        $data,
        array $fields,
        array $associations
    ): array {
        if ($this->isSingleRowOfData($data)) {
            return $this->renderSingleRow($data, $queryBuilder, $fields, $associations);
        } else {
            return $this->renderMany($data, $queryBuilder, $fields, $associations);
        }
    }

    /**
     * @param mixed $data
     * @param QueryBuilder $queryBuilder
     * @param AbstractField[] $fields
     * @param Association[] $associations
     *
     * @return array<int, array<string, mixed>>
     */
    protected function renderMany(
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
    protected function renderSingleRow(
        $row,
        QueryBuilder $queryBuilder,
        array $fields,
        array $associations
    ): array {
        $renderedData = [];

        foreach ($fields as $field) {
            $this->addRenderedField($row, $field, $renderedData);
        }

        foreach ($associations as $association) {
            $this->addRenderedAssociation($row, $association, $renderedData);
        }

        return $renderedData;
    }

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
     * @param mixed $row
     * @param Association $association
     * @param array<string, mixed> $renderedData
     */
    protected function addRenderedAssociation(
        $row,
        Association $association,
        array &$renderedData
    ): void{
        $associationData = $this->valueFromRow($row, $association->getKey());

        if (! $association->passesPolicy($associationData, $row)) {
            return;
        }

        $renderedData[$association->getName()] = $this->doRender(
            $association->getRelatedQueryBuilder(), $associationData,
            $association->getFields() ?? [], $association->getAssociations() ?? []
        );
    }
}
