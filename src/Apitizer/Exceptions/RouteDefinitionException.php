<?php

namespace Apitizer\Exceptions;

use Apitizer\Schema;

class RouteDefinitionException extends ApitizerException
{
    public static function schemaExpected(string $given): self
    {
        $message = "Expected an [Apitizer\Schema] instance, but got [$given]";

        return new static($message);
    }

    public static function associationUndefined(string $associationName, Schema $schema): self
    {
        $class = get_class($schema);
        $message = "Association [$associationName] does not exist on [$class]";

        return new static($message);
    }
}
