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
     * @return (Field|Association)[]
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
     * @param Field|Association $field
     * @throws DefinitionException
     */
    static function validateField(QueryBuilder $queryBuilder, string $name, $field)
    {
        if ($field instanceof Association) {
            $field->setName($name);

            if (! static::isValidAssociation($queryBuilder, $field)) {
                throw DefinitionException::associationDoesNotExist($queryBuilder, $field);
            }

            return $field;
        }

        if (is_string($field)) {
            $field = new Field($queryBuilder, $field, 'any');
        }

        if (!$field instanceof AbstractField) {
            throw DefinitionException::fieldDefinitionExpected($queryBuilder, $name, $field);
        }

        $field->setName($name);

        return $field;
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
     */
    static function validateSorts(QueryBuilder $queryBuilder, array $sorts): array
    {
        foreach ($sorts as $name => $sort) {
            static::validateSort($queryBuilder, $name, $sort);
        }

        return $sorts;
    }

    static function validateSort(QueryBuilder $queryBuilder, string $name, $sort): void
    {
        if (! $sort instanceof Sort) {
            throw DefinitionException::sortDefinitionExpected($queryBuilder, $name, $sort);
        }

        $sort->setName($name);
    }

    /**
     * Validate that each filter has the correct type.
     */
    static function validateFilters(QueryBuilder $queryBuilder, array $filters): array
    {
        foreach ($filters as $name => $filter) {
            static::validateFilter($queryBuilder, $name, $filter);
        }

        return $filters;
    }

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
