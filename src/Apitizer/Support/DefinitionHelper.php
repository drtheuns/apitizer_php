<?php

namespace Apitizer\Support;

use Apitizer\QueryBuilder;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Exceptions\DefinitionException;
use Apitizer\Types\Field;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;

// Separate class so it can be reused by the schema validator without cluttering
// up the query builder methods with more public methods.
class DefinitionHelper
{
    /**
     * Validate that each field has a correct type, possibly assigning the any
     * type.
     *
     * @param QueryBuilder $queryBuilder
     * @param array<string, AbstractField|Association|mixed> $fields
     *
     * @return array<string, AbstractField>
     */
    static function validateFields(QueryBuilder $queryBuilder, array $fields): array
    {
        $castFields = [];

        foreach ($fields as $name => $field) {
            $castFields[$name] = static::validateField($queryBuilder, $name, $field);
        }

        return $castFields;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $name
     * @param AbstractField|mixed $field
     *
     * @throws DefinitionException
     *
     * @return AbstractField
     */
    static function validateField(QueryBuilder $queryBuilder, string $name, $field)
    {
        if (is_string($field)) {
            $field = new Field($queryBuilder, $field, 'any');
        }

        if (!$field instanceof AbstractField) {
            throw DefinitionException::fieldDefinitionExpected($queryBuilder, $name, $field);
        }

        $field->setName($name);

        return $field;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array<string, Association|mixed> $associations
     *
     * @return array<string, Association>
     */
    static function validateAssociations(QueryBuilder $queryBuilder, array $associations): array
    {
        $castFields = [];

        foreach ($associations as $name => $association) {
            $castFields[$name] = static::validateAssociation($queryBuilder, $name, $association);
        }

        return $castFields;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $name
     * @param Association|mixed $association
     */
    static function validateAssociation(
        QueryBuilder $queryBuilder,
        string $name,
        $association
    ): Association {
        if (! $association instanceof Association) {
            throw DefinitionException::associationDefinitionExpected(
                $queryBuilder, $name, $association
            );
        }

        $association->setName($name);

        if (!static::isValidAssociation($queryBuilder, $association)) {
            throw DefinitionException::associationDoesNotExist($queryBuilder, $association);
        }

        return $association;
    }

    private static function isValidAssociation(
        QueryBuilder $queryBuilder,
        Association $association
    ): bool
    {
        $key = $association->getKey();
        $model = $queryBuilder->model();

        return method_exists($model, $key) && $model->{$key}() instanceof EloquentRelation;
    }

    /**
     * Validate that each sort has the correct type.
     *
     * @param QueryBuilder $queryBuilder
     * @param array<string, Sort|mixed> $sorts
     *
     * @return array<string, Sort>
     */
    static function validateSorts(QueryBuilder $queryBuilder, array $sorts): array
    {
        foreach ($sorts as $name => $sort) {
            static::validateSort($queryBuilder, $name, $sort);
        }

        return $sorts;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $name
     * @param Sort|mixed $sort
     */
    static function validateSort(QueryBuilder $queryBuilder, string $name, $sort): void
    {
        if (! $sort instanceof Sort) {
            throw DefinitionException::sortDefinitionExpected($queryBuilder, $name, $sort);
        }

        if (! $sort->getHandler()) {
            throw DefinitionException::sortHandlerNotDefined($queryBuilder, $name);
        }

        $sort->setName($name);
    }

    /**
     * Validate that each filter has the correct type.
     *
     * @param QueryBuilder $queryBuilder
     * @param array<string, Filter|mixed> $filters
     *
     * @return array<string, Filter>
     */
    static function validateFilters(QueryBuilder $queryBuilder, array $filters): array
    {
        foreach ($filters as $name => $filter) {
            static::validateFilter($queryBuilder, $name, $filter);
        }

        return $filters;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $name
     * @param Filter|mixed $filter
     *
     * @throws DefinitionException
     */
    static function validateFilter(QueryBuilder $queryBuilder, string $name, $filter): void
    {
        if (! $filter instanceof Filter) {
            throw DefinitionException::filterDefinitionExpected($queryBuilder, $name, $filter);
        }

        $filter->setName($name);

        if (! $filter->getHandler()) {
            throw DefinitionException::filterHandlerNotDefined($queryBuilder, $filter);
        }
    }
}
