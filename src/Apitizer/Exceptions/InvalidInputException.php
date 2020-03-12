<?php

namespace Apitizer\Exceptions;

use Apitizer\Apitizer;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;
use Apitizer\Types\Factory;
use Apitizer\Schema;

/**
 * This error occurs when the client passes invalid data.
 *
 * For example, if a filter expects an array of strings, but an integer is
 * given, this exception would be thrown.
 */
class InvalidInputException extends ApitizerException
{
    /**
     * The schema where the exception occurred.
     *
     * @var Schema
     */
    public $schema;

    /**
     * The class from which the exception originates
     *
     * @var Factory
     */
    public $origin;

    /**
     * @var Filter|Sort
     */
    public $instance;

    /**
     * @var string 'filter' | 'sort'
     */
    public $namespace;

    /**
     * @param Filter $filter
     * @param mixed $given
     */
    public static function filterTypeError(Filter $filter, $given): self
    {
        $filterKey = Apitizer::getFilterKey();
        $filterName = $filter->getName();
        $type = gettype($given);
        $expectedType = $filter->getInputType();
        $filterParam = $filterKey . '[' . $filterName . ']';
        $message = "Expected $filterParam to receive [$expectedType] got [$type]";

        $e = new static($message);
        $e->instance = $filter;
        $e->schema = $filter->getSchema();

        return $e;
    }

    public static function undefinedFilterCalled(string $name, Schema $schema): self
    {
        $class = get_class($schema);
        $message = "Filter $name does not exist on [$class]";

        $e = new static($message);
        $e->namespace = 'filter';
        $e->schema = $schema;

        return $e;
    }

    public static function undefinedSortCalled(string $name, Schema $schema): self
    {
        $class = get_class($schema);
        $message = "Sort $name does not exist on [$class]";

        $e = new static($message);
        $e->namespace = 'sort';
        $e->schema = $schema;

        return $e;
    }
}
