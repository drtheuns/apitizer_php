<?php

namespace Apitizer\Exceptions;

use Apitizer\QueryBuilder;
use Apitizer\Types\Association;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;

/**
 * This exception occurs when the programmer gives an unexpected definition in
 * the query builder.
 */
class DefinitionException extends ApitizerException
{
    /**
     * @var QueryBuilder The query builder where the definition error lies.
     */
    protected $queryBuilder;

    /**
     * @var string one of the namespaces from the NAMESPACES const.
     */
    protected $namespace;

    const NAMESPACES = ['association', 'field', 'filter', 'sort'];

    /**
     * @var string|null The field/sort/filter name where this exception occured.
     */
    protected $name;

    public function __construct(
        string $message,
        QueryBuilder $queryBuilder,
        string $namespace,
        string $name = null
    ) {
        parent::__construct($message);
        $this->queryBuilder = $queryBuilder;
        $this->namespace = $namespace;
        $this->name = $name;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $key
     * @param mixed $given
     */
    static function builderClassExpected(QueryBuilder $queryBuilder, string $key, $given): self
    {
        $class = get_class($queryBuilder);
        $message = "Expected association by [$key] on [$class] to be a "
                 ."query builder class but got [$given]";

        return new static($message, $queryBuilder, 'association');
    }

    static function associationDoesNotExist(QueryBuilder $queryBuilder, Association $associaton): self
    {
        $name = $associaton->getName();
        $class = get_class($queryBuilder);
        $key = $associaton->getKey();
        $modelClass = get_class($queryBuilder->model());
        $message = "Association [$name] on [$class] refers to association [$key] which"
            . " does not exist on the model [$modelClass]";


        return new static($message, $queryBuilder, 'association', $associaton->getName());
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $name
     * @param mixed $given
     */
    static function fieldDefinitionExpected(QueryBuilder $queryBuilder, string $name, $given): self
    {
        $class = get_class($queryBuilder);
        $type = is_object($given) ? get_class($given) : gettype($given);
        $message = "Expected [$name] on [$class] to be a field definition, but got"
                 . " a [$type]";

        return new static($message, $queryBuilder, 'field', $name);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $name
     * @param mixed $given
     */
    static function filterDefinitionExpected(QueryBuilder $queryBuilder, string $name, $given): self
    {
        $class = get_class($queryBuilder);
        $type = is_object($given) ? get_class($given) : gettype($given);
        $message = "Expected [$name] on [$class] to be a filter definition, but got"
                 . " a [$type]";

        return new static($message, $queryBuilder, 'filter', $name);
    }

    static function filterHandlerNotDefined(QueryBuilder $queryBuilder, Filter $filter): self
    {
        $class = get_class($queryBuilder);
        $name = $filter->getName();
        $message = "Filter [$name] on [$class] does not have a handler defined";

        return new static($message, $queryBuilder, 'filter', $filter->getName());
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $name
     * @param mixed $given
     */
    static function sortDefinitionExpected(QueryBuilder $queryBuilder, string $name, $given): self
    {
        $class = get_class($queryBuilder);
        $type = is_object($given) ? get_class($given) : gettype($given);
        $message = "Expected [$name] on [$class] to be a sort definition, but got"
            . " a [$type]";

        return new static($message, $queryBuilder, 'sort', $name);
    }

    static function sortHandlerNotDefined(QueryBuilder $queryBuilder, string $name): self
    {
        $class = get_class($queryBuilder);
        $message = "Expected a callable handler to be defined for sort [$name] on [$class]";

        return new static($message, $queryBuilder, 'sort', $name);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
