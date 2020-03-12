<?php

namespace Apitizer\Exceptions;

use Apitizer\Schema;
use Apitizer\Types\Association;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;

/**
 * This exception occurs when the programmer gives an unexpected definition in
 * the schema.
 */
class DefinitionException extends ApitizerException
{
    /**
     * @var Schema The schema where the definition error lies.
     */
    protected $schema;

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
        Schema $schema,
        string $namespace,
        string $name = null
    ) {
        parent::__construct($message);
        $this->schema = $schema;
        $this->namespace = $namespace;
        $this->name = $name;
    }

    /**
     * @param Schema $schema
     * @param string $key
     * @param mixed $given
     */
    public static function schemaClassExpected(Schema $schema, string $key, $given): self
    {
        $class = get_class($schema);
        $message = "Expected association by [$key] on [$class] to be a "
                 ."schema class but got [$given]";

        return new static($message, $schema, 'association');
    }

    public static function associationDoesNotExist(Schema $schema, Association $associaton): self
    {
        $name = $associaton->getName();
        $class = get_class($schema);
        $key = $associaton->getKey();
        $modelClass = get_class($schema->model());
        $message = "Association [$name] on [$class] refers to association [$key] which"
            . " does not exist on the model [$modelClass]";


        return new static($message, $schema, 'association', $associaton->getName());
    }

    /**
     * @param Schema $schema
     * @param string $name
     * @param mixed $given
     */
    public static function fieldDefinitionExpected(Schema $schema, string $name, $given): self
    {
        $class = get_class($schema);
        $type = is_object($given) ? get_class($given) : gettype($given);
        $message = "Expected [$name] on [$class] to be a field definition, but got"
                 . " a [$type]";

        return new static($message, $schema, 'field', $name);
    }

    /**
     * @param Schema $schema
     * @param string $name
     * @param mixed $given
     */
    public static function associationDefinitionExpected(
        Schema $schema,
        string $name,
        $given
    ): self {
        $class = get_class($schema);
        $type = is_object($given) ? get_class($given) : gettype($given);
        $message = "Expected [$name] on [$class] to be an \Apitizer\Types\Association, "
                 . "but got a [$type]";

        return new static($message, $schema, 'association', $name);
    }

    /**
     * @param Schema $schema
     * @param string $name
     * @param mixed $given
     */
    public static function filterDefinitionExpected(Schema $schema, string $name, $given): self
    {
        $class = get_class($schema);
        $type = is_object($given) ? get_class($given) : gettype($given);
        $message = "Expected [$name] on [$class] to be a filter definition, but got"
                 . " a [$type]";

        return new static($message, $schema, 'filter', $name);
    }

    public static function filterHandlerNotDefined(Schema $schema, Filter $filter): self
    {
        $class = get_class($schema);
        $name = $filter->getName();
        $message = "Filter [$name] on [$class] does not have a handler defined";

        return new static($message, $schema, 'filter', $filter->getName());
    }

    public static function filterExpectRequired(Schema $schema, Filter $filter): self
    {
        $class = get_class($schema);
        $name = $filter->getName();
        $message = "Filter [$name] on [$class] should call expect() before defining array element type";

        return new static($message, $schema, 'filter', $filter->getName());
    }

    /**
     * @param Schema $schema
     * @param string $name
     * @param mixed $given
     */
    public static function sortDefinitionExpected(Schema $schema, string $name, $given): self
    {
        $class = get_class($schema);
        $type = is_object($given) ? get_class($given) : gettype($given);
        $message = "Expected [$name] on [$class] to be a sort definition, but got"
            . " a [$type]";

        return new static($message, $schema, 'sort', $name);
    }

    public static function sortHandlerNotDefined(Schema $schema, string $name): self
    {
        $class = get_class($schema);
        $message = "Expected a callable handler to be defined for sort [$name] on [$class]";

        return new static($message, $schema, 'sort', $name);
    }

    public function getSchema(): Schema
    {
        return $this->schema;
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
