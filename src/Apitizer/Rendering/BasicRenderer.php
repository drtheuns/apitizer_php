<?php

namespace Apitizer\Rendering;

use Apitizer\Policies\PolicyFailed;
use Apitizer\Schema;
use Apitizer\Types\FetchSpec;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Exceptions\InvalidOutputException;

class BasicRenderer extends AbstractRenderer implements Renderer
{
    public function render(Schema $schema, $data, FetchSpec $fetchSpec): array
    {
        return $this->doRender(
            $schema,
            $data,
            $fetchSpec->getFields(),
            $fetchSpec->getAssociations()
        );
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
            $association->getRelatedSchema(),
            $associationData,
            $association->getFields() ?? [],
            $association->getAssociations() ?? []
        );
    }

    /**
     * @param mixed $row
     * @param Schema $schema
     * @param AbstractField[] $fields
     * @param Association[] $associations
     *
     * @return array<string, mixed>
     */
    protected function renderSingleRow(
        $row,
        Schema $schema,
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

    
}
