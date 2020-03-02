<?php

namespace Apitizer\Rendering;

use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\JsonApi\Document;
use Apitizer\JsonApi\Resource;
use Apitizer\Policies\PolicyFailed;
use Apitizer\QueryBuilder;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Types\FetchSpec;
use ArrayAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionClass;

class JsonApiRenderer extends AbstractRenderer implements Renderer
{
    /** @var Document */
    protected $document;

    public function __construct()
    {
        $this->document = new Document();
    }

    /**
      * @param QueryBuilder $queryBuilder
      * @param mixed $data
      * @param FetchSpec $fetchSpec
      * @return array
      */
    public function render(QueryBuilder $queryBuilder, $data, FetchSpec $fetchSpec): array
    {
        $attributes = [];
        if ($this->isSingleRowOfData($data)) {
            $data = collect([$data]);
        }

        foreach ($data as $row) {
            foreach ($fetchSpec->getFields() as $field) {
                $this->addRenderedField($row, $field, $attributes);
            }

            $this->document->addResource(
                $this->getResourceType($queryBuilder, $row),
                $this->getResourceId($queryBuilder, $row),
                $attributes,
            );
        }

        return $this->document->toArray();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param mixed $row
     * @return string
     */
    protected function getResourceType(QueryBuilder $queryBuilder, $row): string
    {
        if ($row instanceof Resource) {
            return $row->getResourceType();
        }

        $className = (new ReflectionClass($queryBuilder->model()))->getShortName();

        return Str::snake(Str::plural($className));
    }

    /**
     * @throws InvalidOutputException
     * @param QueryBuilder $queryBuilder
     * @param mixed $row
     * @return string
     */
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
