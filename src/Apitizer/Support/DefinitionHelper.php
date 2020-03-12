<?php

namespace Apitizer\Support;

use Apitizer\Schema;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Exceptions\DefinitionException;
use Apitizer\Types\Field;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;

// Separate class so it can be reused by the schema validator without cluttering
// up the schema methods with more public methods.
class DefinitionHelper
{
    /**
     * Validate that each field has a correct type, possibly assigning the any
     * type.
     *
     * @param Schema $schema
     * @param array<string, AbstractField|Association|mixed> $fields
     *
     * @return array<string, AbstractField>
     */
    public static function validateFields(Schema $schema, array $fields): array
    {
        $castFields = [];

        foreach ($fields as $name => $field) {
            $castFields[$name] = static::validateField($schema, $name, $field);
        }

        return $castFields;
    }

    /**
     * @param Schema $schema
     * @param string $name
     * @param AbstractField|mixed $field
     *
     * @throws DefinitionException
     *
     * @return AbstractField
     */
    public static function validateField(Schema $schema, string $name, $field)
    {
        if (is_string($field)) {
            $field = new Field($schema, $field, 'any');
        }

        if (!$field instanceof AbstractField) {
            throw DefinitionException::fieldDefinitionExpected($schema, $name, $field);
        }

        $field->setName($name);

        return $field;
    }

    /**
     * @param Schema $schema
     * @param array<string, Association|mixed> $associations
     *
     * @return array<string, Association>
     */
    public static function validateAssociations(Schema $schema, array $associations): array
    {
        $castFields = [];

        foreach ($associations as $name => $association) {
            $castFields[$name] = static::validateAssociation($schema, $name, $association);
        }

        return $castFields;
    }

    /**
     * @param Schema $schema
     * @param string $name
     * @param Association|mixed $association
     */
    public static function validateAssociation(
        Schema $schema,
        string $name,
        $association
    ): Association {
        if (! $association instanceof Association) {
            throw DefinitionException::associationDefinitionExpected(
                $schema,
                $name,
                $association
            );
        }

        $association->setName($name);

        if (!static::isValidAssociation($schema, $association)) {
            throw DefinitionException::associationDoesNotExist($schema, $association);
        }

        return $association;
    }

    private static function isValidAssociation(
        Schema $schema,
        Association $association
    ): bool {
        $key = $association->getKey();
        $model = $schema->model();

        return method_exists($model, $key) && $model->{$key}() instanceof EloquentRelation;
    }

    /**
     * Validate that each sort has the correct type.
     *
     * @param Schema $schema
     * @param array<string, Sort|mixed> $sorts
     *
     * @return array<string, Sort>
     */
    public static function validateSorts(Schema $schema, array $sorts): array
    {
        foreach ($sorts as $name => $sort) {
            static::validateSort($schema, $name, $sort);
        }

        return $sorts;
    }

    /**
     * @param Schema $schema
     * @param string $name
     * @param Sort|mixed $sort
     */
    public static function validateSort(Schema $schema, string $name, $sort): void
    {
        if (! $sort instanceof Sort) {
            throw DefinitionException::sortDefinitionExpected($schema, $name, $sort);
        }

        if (! $sort->getHandler()) {
            throw DefinitionException::sortHandlerNotDefined($schema, $name);
        }

        $sort->setName($name);
    }

    /**
     * Validate that each filter has the correct type.
     *
     * @param Schema $schema
     * @param array<string, Filter|mixed> $filters
     *
     * @return array<string, Filter>
     */
    public static function validateFilters(Schema $schema, array $filters): array
    {
        foreach ($filters as $name => $filter) {
            static::validateFilter($schema, $name, $filter);
        }

        return $filters;
    }

    /**
     * @param Schema $schema
     * @param string $name
     * @param Filter|mixed $filter
     *
     * @throws DefinitionException
     */
    public static function validateFilter(Schema $schema, string $name, $filter): void
    {
        if (! $filter instanceof Filter) {
            throw DefinitionException::filterDefinitionExpected($schema, $name, $filter);
        }

        $filter->setName($name);

        if (! $filter->getHandler()) {
            throw DefinitionException::filterHandlerNotDefined($schema, $filter);
        }
    }
}
