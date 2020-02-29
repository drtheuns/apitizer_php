<?php

namespace Apitizer\Rendering;

use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\JsonApi\Resource;
use Apitizer\QueryBuilder;
use Apitizer\Policies\PolicyFailed;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Types\FetchSpec;
use ArrayAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionClass;

class JsonApiRenderer extends AbstractRenderer implements Renderer
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
    public function renderSingleRow(
        $row,
        QueryBuilder $queryBuilder,
        array $fields,
        array $associations
    ): array {
        $attributes = [];
        foreach ($fields as $field) {
            $this->addRenderedField($row, $field, $attributes);
        }

        return [
            'id'         => $this->getResourceId($queryBuilder, $row),
            'type'       => $this->getResourceType($queryBuilder, $row),
            'attributes' => $attributes,
        ];
    }

    protected function getResourceType(QueryBuilder $queryBuilder, $row)
    {
        if ($row instanceof Resource) {
            return $row->getResourceType();
        }

        $className = (new ReflectionClass($queryBuilder->model()))->getShortName();

        return Str::snake($className);
    }

    protected function getResourceId(QueryBuilder $queryBuilder, $row): string
    {
        if ($row instanceof Resource) {
            return $row->getResourceId();
        }

        if ($row instanceof Model) {
            return (string) $row->getKey();
        }

        if (is_array($row) || $row instanceof ArrayAccess) {
            if (isset($row['id'])) {
                return (string) $row['id'];
            }

            if (isset($row['uuid'])) {
                return (string) $row['uuid'];
            }
        }

        if (is_object($row)) {
            if (isset($row->{'id'})) {
                return (string) $row->{'id'};
            }

            if (isset($row->{'uuid'})) {
                return (string) $row->{'uuid'};
            }
        }

        throw InvalidOutputException::noJsonApiIdentifier($queryBuilder, $row);
    }
}
