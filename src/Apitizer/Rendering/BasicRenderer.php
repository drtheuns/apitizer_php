<?php

namespace Apitizer\Rendering;

use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\Policies\PolicyFailed;
use Apitizer\QueryBuilder;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Types\FetchSpec;

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
