<?php

namespace Apitizer\Exceptions;

/**
 * This exception occurs when the programmer gives an unexpected definition in
 * the query builder.
 */
class DefinitionException extends ApitizerException
{
    /**
     * The query builder where the definition error lies.
     */
    protected $queryBuilder;

    /**
     * @var 'filter'|'sort'|'field'|'association'|'other'
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $name;

    public static function builderClassExpected(string $givenClass)
    {
        return new static("Expected [$givenClass] to be a query builder class");
    }

    public static function fieldDefinitionExpected(string $fieldName, $given)
    {
        $type = gettype($given);
        return new static("Unexpected field type for [$fieldName]: {$type}");
    }

    public static function filterDefinitionExpected(string $filterName, $given)
    {
        $type = gettype($given);
        return new static("Unexpected filter type for [$filterName]: {$type}");
    }

    public static function sortDefinitionExpected(string $sortName, $given)
    {
        $type = gettype($given);
        return new static("Unexpected sort type for [$sortName]: {$type}");
    }

    public static function associationDoesNotExist(string $key)
    {
        // TODO: Improve this message to include the model.
        return new static("An association by the name $key does not exist");
    }
}
