<?php

namespace Apitizer\Exceptions;

class SchemaDefinitionException extends ApitizerException
{
    public static function notAQueryBuilder($given)
    {
        return new static("Expected a query builder to be given but got [$given]");
    }

    public static function namespaceLookupFailed(string $namespace, ClassFinderException $e)
    {
        return new static("Failed to find query builders in [$namespace]", 0, $e);
    }
}
