<?php

namespace Apitizer\Rendering;

use ArrayAccess;
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
     * @var array<string, mixed>
     */
    protected $includes = [];

    public function render(QueryBuilder $queryBuilder, $data, FetchSpec $fetchSpec): array
    {
        $render = [];
        $render['data'] = $this->doRender(
            $queryBuilder, $data,
            $fetchSpec->getFields(),
            $fetchSpec->getAssociations()
        );
        $render['included'] = $this->includes;
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
        $attributes = [];
        $relationships = [];
        foreach ($fields as $field) {
            $this->addRenderedField($row, $field, $attributes);
        }
        foreach ($associations as $association) {
            $this->addRenderedAssociation($row, $association, $relationships, $queryBuilder);
        }

        return [
            'type'          => $this->getResourceType($queryBuilder, $row),
            'id'            => $this->getResourceId($queryBuilder, $row),
            'attributes'    => $attributes,
            'relationships' => $relationships,
        ];
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
     * @param QueryBuilder $queryBuilder
     */
    protected function addRenderedAssociation($row, Association $association, array &$renderedData, Querybuilder $queryBuilder): void
    {
        $associationData = $this->valueFromRow($row, $association->getKey());

        if (! $association->passesPolicy($associationData, $row)) {
            return;
        }

        //If it only contains an id.
        $this->addRenderedAssociationSingleElement($row, $association, $associationData, $renderedData, $queryBuilder);
        //If it contains more than just an id.
        $this->addRenderedAssociationMultiElement($row, $association, $associationData, $renderedData, $queryBuilder);
        
   
    }

    /**
     * Render the association data if only the id is provided
     *
     * @param mixed $row
     * @param Association $association
     * @param object $associationData
     * @param array<string, mixed> $renderedData
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    protected function addRenderedAssociationSingleElement($row, Association $association, object $associationData, array &$renderedData, $queryBuilder): void
    {
        $count = 0;
        foreach ($associationData as $data) 
        {
            if (!is_bool($data)) {
                $renderedData[$association->getName()]['data'][$count]['type'] = Str::snake(class_basename($data));
                $renderedData[$association->getName()]['data'][$count]['id'] = $this->getResourceId($queryBuilder, $row);
                $count++;
            }
        }
    }

    /**
     * Render the association data if more than the id is provided
     *
     * @param mixed $row
     * @param Association $association
     * @param object $associationData
     * @param array<string, mixed> $renderedData
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    protected function addRenderedAssociationMultiElement($row, Association $association, object $associationData, array &$renderedData, $queryBuilder): void
    {
        $count = 0;
        foreach ($associationData->only('id') as $data) 
        {
            $renderedData[$association->getName()]['data'][$count]['type'] = Str::snake(class_basename($associationData));
            $renderedData[$association->getName()]['data'][$count]['id'] = $this->getResourceId($queryBuilder, $row);
            $count++;
        }
    }










    // public function render(QueryBuilder $queryBuilder, $data, FetchSpec $fetchSpec): array
    // {
    //     return $this->renderManyRow(
    //         $queryBuilder,
    //         $data,
    //         $fetchSpec->getFields(),
    //         $fetchSpec->getAssociations()
    //     );
    // }

    // /**
    //  * @param mixed $row
    //  * @param QueryBuilder $queryBuilder
    //  * @param AbstractField[] $fields
    //  * @param Association[] $associations
    //  *
    //  * @return array<string, mixed>
    //  */
    // protected function renderManyRow(
    //     QueryBuilder $queryBuilder,
    //     $rows,
    //     array $fields,
    //     array $associations
    // ): array {
    //     $renderedData = [];

    //     // $renderedData['type'] = "posts";

        
    //     $rowCount = 0;
    //     foreach($rows as $row){
    //         //dd($row;)
    //         $renderedData[]['type'] = Str::plural($this->getResourceType($queryBuilder, $rows));
    //         $renderedData[]['id'] = $row->getKey();

    //         // foreach($fields as $field){
    //         //     $renderedData[$rowCount]['attributes'][$field->getName()] = $field->render($row, $this);
    //         // }
    //         $renderedData = $this->renderFields($renderedData, $fields, $row);
    //         $className = (new ReflectionClass($queryBuilder->model()));
    //         $availableMethods = $className->getMethods();
    //         //dd($row);
    //         //dd($availableMethods[0]->getReturnType());
    //         //dd($row->getTraits());

    //         $relations = [];
    //         foreach ($className->getMethods() as $reflectionMethod) {
    //             $returnType = $reflectionMethod->getReturnType();
    //             if ($returnType) {
    //                 if (in_array(class_basename($returnType->getName()), ['HasOne', 'HasMany', 'BelongsTo', 'BelongsToMany', 'MorphToMany', 'MorphTo'])) {
    //                     $relations[] = $reflectionMethod;
    //                 }
    //             }
    //         }

    //         //dd($relations);
    //         foreach($relations as $relation){
    //             // dd($row);
    //             $relationName = $relation->getName();
    //             $renderedData[]['relationships'][$relationName]['links']['self']  = "url";
    //             $renderedData[]['relationships'][$relationName]['links']['related'] = "url";
    //             $renderedData[]['relationships'][$relationName]['data']['type'] = $relationName;
    //             $renderedData[]['relationships'][$relationName]['data']['id'] = $row->author;
    //             // $renderedData[$rowCount]['relationships'][$relationName]['data']['id'] = $row->$relationName->id;
    //             //dd($row->$relationName());
    //         }

    //         $rowCount++;
    //     }

    //     // foreach ($fields as $field) {
    //     //     $this->addRenderedField($row, $field, $renderedData);
    //     // }

    //     // foreach ($associations as $association) {
    //     //     $this->addRenderedAssociation($row, $association, $renderedData);
    //     // }

    //     return $renderedData;
    // }


    // private function renderFields(array $renderedData, array $fields, object $row)
    // {
    //     foreach($fields as $field){
    //         $renderedData[]['attributes'][$field->getName()] = $field->render($row, $this);
    //     }
    //     return $renderedData;
    // }





















    // public function render(QueryBuilder $queryBuilder, $data, FetchSpec $fetchSpec): array
    // {
 
    //     if ($this->isSingleRowOfData($data)) {
    //         $response['data'] = $this->renderOne($data, $selectedFields);
    //         return $response;
    //     }
    //     else {         

    //         $response['data'] = $this->renderMultiplefields($queryBuilder, $data, $fetchSpec);
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

    // protected function renderMultiplefields(QueryBuilder $queryBuilder, $row, FetchSpec $selectedFields)
    // {
    //     return $row[0];
    //     dd($row[0], $selectedFields);

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
}
