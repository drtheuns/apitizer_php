<?php

namespace Apitizer\Rendering;

use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\JsonApi\Resource;
use Apitizer\QueryBuilder;
use Apitizer\Policies\PolicyFailed;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use ArrayAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionClass;

// class JsonApiRenderer extends AbstractRenderer implements Renderer
// {
    // public function render(QueryBuilder $queryBuilder, $data, array $selectedFields): array
    // {
    //     // We're only concerned with the happy path in this renderer. If any
    //     // errors occur, such as an invalid filter, they will be caught
    //     // beforehand during the processing of those filters.
    //     $response = [];

    //     if ($this->isSingleRowOfData($data)) {
    //         $response['data'] = $this->renderOne($data, $selectedFields);
    //         return $response;
    //     }

    //     return [];
    // }

    // protected function renderOne(QueryBuilder $queryBuilder, $row, array $selectedFields): array
    // {
    //     $resource = [
    //         'id'   => $this->getResourceId($queryBuilder, $row),
    //         'type' => $this->getResourceType($queryBuilder, $row),
    //     ];

    //     return [];
    // }

    // protected function getResourceType(QueryBuilder $queryBuilder, $row)
    // {
    //     if ($row instanceof Resource) {
    //         return $row->getResourceType();
    //     }

    //     $className = (new ReflectionClass($queryBuilder->model()))->getShortName();

    //     return Str::snake($className);
    // }

    // protected function getResourceId(QueryBuilder $queryBuilder, $row): string
    // {
    //     if ($row instanceof Resource) {
    //         return $row->getResourceId();
    //     }

    //     if ($row instanceof Model) {
    //         return (string) $row->getKey();
    //     }

    //     if (is_array($row) || $row instanceof ArrayAccess) {
    //         if (isset($row['id'])) {
    //             return (string) $row['id'];
    //         }

    //         if (isset($row['uuid'])) {
    //             return (string) $row['uuid'];
    //         }
    //     }

    //     if (is_object($row)) {
    //         if (isset($row->{'id'})) {
    //             return (string) $row->{'id'};
    //         }

    //         if (isset($row->{'uuid'})) {
    //             return (string) $row->{'uuid'};
    //         }
    //     }

    //     throw InvalidOutputException::noJsonApiIdentifier($queryBuilder, $row);
    // }
// }
