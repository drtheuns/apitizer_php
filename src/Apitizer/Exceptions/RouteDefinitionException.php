<?php

namespace Apitizer\Exceptions;

use Apitizer\QueryBuilder;

class RouteDefinitionException extends ApitizerException
{
    public static function queryBuilderExpected(string $given): self
    {
        $message = "Expected an [Apitizer\QueryBuilder] instance, but got [$given]";

        return new static($message);
    }

    public static function associationUndefined(string $associationName, QueryBuilder $schema): self
    {
        $class = get_class($schema);
        $message = "Association [$associationName] does not exist on [$class]";

        return new static($message);
    }
}
