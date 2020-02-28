<?php

namespace Apitizer\Exceptions;

use Apitizer\Apitizer;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;
use Apitizer\Types\Factory;
use Apitizer\QueryBuilder;

/**
 * This error occurs when the client passes invalid data.
 *
 * For example, if a filter expects an array of strings, but an integer is
 * given, this exception would be thrown.
 */
class InvalidInputException extends ApitizerException
{
    /**
     * The query builder where the exception occurred.
     *
     * @var QueryBuilder
     */
    public $queryBuilder;

    /**
     * The class from which the exception originates
     *
     * @var Factory
     */
    public $origin;

    /**
     * @var Filter|Sort
     */
    public $type;

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
        $e->type = $filter;
        $e->queryBuilder = $filter->getQueryBuilder();

        return $e;
    }
}
