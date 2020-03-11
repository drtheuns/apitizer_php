<?php

namespace Apitizer\Rendering;

use ArrayAccess;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use ReflectionClass;
use Apitizer\QueryBuilder;
use Illuminate\Support\Str;
use Apitizer\Types\FetchSpec;
use Apitizer\JsonApi\Resource;
use Apitizer\Types\Association;
use Apitizer\Types\AbstractField;
use Apitizer\Policies\PolicyFailed;
use Illuminate\Database\Eloquent\Model;
use Apitizer\Exceptions\InvalidOutputException;

class JsonApiRenderer extends AbstractRenderer implements Renderer
{
    /**
     * @var array<string, array<string, array<string, mixed>>>
     */
    protected $included = [];

    public function paginate(QueryBuilder $queryBuilder, LengthAwarePaginator $paginator, FetchSpec $fetchSpec)
    {
        /** @var LengthAwarePaginator */
        $paginator = parent::paginate($queryBuilder, $paginator, $fetchSpec);
        $paginatedData = $paginator->toArray();

        $rootObject = $paginatedData['data'];
        unset($paginatedData['data']);

        // This is to support the JSON-API pagination format:
        // https://jsonapi.org/format/#fetching-pagination
        $rootObject['links'] = [
            'first' => $paginatedData['first_page_url'],
            'last' => $paginatedData['last_page_url'],
            'prev' => $paginatedData['prev_page_url'],
            'next' => $paginatedData['next_page_url'],
        ];

        // This additional information is not part of the spec, so it must be
        // put in the meta.
        $rootObject['meta'] = [
            'pagination' => [
                'from'         => $paginatedData['from'],
                'to'           => $paginatedData['to'],
                'total'        => $paginatedData['total'],
                'current_page' => $paginatedData['current_page'],
                'last_page'    => $paginatedData['last_page'],
                'per_page'     => $paginatedData['per_page'],
            ],
        ];

        return $rootObject;
    }

    public function render(QueryBuilder $queryBuilder, $data, FetchSpec $fetchSpec): array
    {
        $render = [];
        $render['data'] = $this->doRender(
            $queryBuilder, $data,
            $fetchSpec->getFields(),
            $fetchSpec->getAssociations()
        );

        $render['included'] = collect($this->included)->flatMap(function ($include) {
            return array_values($include);
        })->values()->all();

        $this->included = [];

        return $render;
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
        $data = $this->renderOneWithoutRelations($row, $queryBuilder, $fields);

        $this->renderAssociations($row, $associations, $data);

        return $data;
    }

    /**
     * @param mixed $row
     * @param QueryBuilder $queryBuilder
     * @param AbstractField[] $fields
     * @return array{type: string, id: string, attributes: array<string, mixed>}
     */
    protected function renderOneWithoutRelations($row, QueryBuilder $queryBuilder, array $fields)
    {
        $attributes = [];
        foreach ($fields as $field) {
            $this->addRenderedField($row, $field, $attributes);
        }

        return [
            'type'          => $this->getResourceType($queryBuilder, $row),
            'id'            => $this->getResourceId($queryBuilder, $row),
            'attributes'    => $attributes,
        ];
    }

    /**
     * @param mixed $row
     * @param Association[] $associations
     * @param array<string, mixed> $resource
     */
    protected function renderAssociations($row, array $associations, array &$resource): void
    {
        $relationships = [];
        foreach ($associations as $association) {
            $this->addRenderedAssociation($row, $association, $relationships);
        }

        if (! empty($relationships)) {
            $resource['relationships'] = $relationships;
        }
    }

    /**
     * Get the type of a resource.
     *
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

        return Str::snake($className);
    }

    /**
     * Get the id or uuid of the resource.
     *
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

    /**
     * Render the associations of a resource.
     *
     * @param mixed $row
     * @param Association $association
     * @param array<string, mixed> $renderedData
     */
    protected function addRenderedAssociation($row, Association $association, array &$renderedData): void
    {
        $queryBuilder = $association->getRelatedQueryBuilder();
        $associationData = $this->valueFromRow($row, $association->getKey());

        if (! $association->passesPolicy($associationData, $row)) {
            return;
        }

        // Generate the links for the original resource
        if ($this->isSingleRowOfData($associationData)) {
            $links = $this->generateResourceReference($associationData, $queryBuilder);
            $this->addIncludedResource($associationData, $association, $association->getRelatedQueryBuilder());
        } else {
            $links = [];

            foreach ($associationData as $row) {
                $links[] = $this->generateResourceReference($row, $queryBuilder);

                $this->addIncludedResource($row, $association, $association->getRelatedQueryBuilder());
            }
        }

        $renderedData[$association->getName()]['data'] = $links;
    }

    /**
     * @param mixed $row
     * @param QueryBuilder $queryBuilder
     * @return array{type: string, id: string}
     */
    protected function generateResourceReference($row, QueryBuilder $queryBuilder): array
    {
        return [
            'type' => $this->getResourceType($queryBuilder, $row),
            'id'   => $this->getResourceId($queryBuilder, $row),
        ];
    }

    /**
     * @param mixed $row
     * @param Association $association
     * @param QueryBuilder $queryBuilder
     */
    protected function addIncludedResource($row, Association $association, QueryBuilder $queryBuilder): void
    {
        $type = $this->getResourceType($queryBuilder, $row);
        $id = $this->getResourceId($queryBuilder, $row);

        // If it already exists, add any missing fields/associations.
        if ($this->isIncluded($type, $id)) {
            $resource = &$this->included[$type][$id];

            // Add any missing associations or fields.
            foreach ($association->getFields() ?? [] as $field) {
                if (! isset($resource['attributes'][$field->getName()])) {
                    $this->addRenderedField($row, $field, $resource['attributes']);
                }
            }

            foreach ($association->getAssociations() ?? [] as $childAssociation) {
                if (! isset($resource['relationships'])) {
                    $resource['relationships'] = [];
                }

                if (! isset($resource['relationships'][$childAssociation->getName()])) {
                    $this->addRenderedAssociation($row, $childAssociation, $resource['relationships']);
                }
            }

        }

        // Otherwise, we're going to create the resource from scratch.
        else {
            // Before we start rendering the associations, we need to first add
            // the resource to the includes.
            // If we have for example: comments -> author -> comments, then the
            // child comments would have been rendered first, but the parent
            // comments are not aware of this, and will overwrite the child.
            $this->included[$type][$id] = $this->renderOneWithoutRelations(
                $row,
                $queryBuilder,
                $association->getFields() ?? [],
            );

            $resource = &$this->included[$type][$id];

            $this->renderAssociations($row, $association->getAssociations() ?? [], $resource);
        }
    }

    protected function isIncluded(string $type, string $id): bool
    {
        return isset($this->included[$type][$id]);
    }
}
