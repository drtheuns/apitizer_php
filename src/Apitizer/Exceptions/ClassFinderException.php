<?php

namespace Apitizer\Exceptions;

class ClassFinderException extends ApitizerException
{
    public static function composerFileNotFound(string $path)
    {
        return new static("Could not find composer file on path [$path]");
    }

    public static function psr4NotFound()
    {
        return new static("Could not find PSR-4 definition in the composer.json");
    }
}
