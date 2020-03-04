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

    /**
     * @param QueryBuilder $queryBuilder
     * @param mixed $data
     * @param FetchSpec $fetchSpec
     * @return array
     */
    public function render(QueryBuilder $queryBuilder, $data, FetchSpec $fetchSpec): array
    {
        return $this->doRender(
            $queryBuilder,
            $data,
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
    public function doRender(
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
    ): void {
        $associationData = $this->valueFromRow($row, $association->getKey());

        if (! $association->passesPolicy($associationData, $row)) {
            return;
        }

        $renderedData[$association->getName()] = $this->doRender(
            $association->getRelatedQueryBuilder(),
            $associationData,
            $association->getFields() ?? [],
            $association->getAssociations() ?? []
        );
    }
}
