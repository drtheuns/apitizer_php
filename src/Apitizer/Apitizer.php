<?php

namespace Apitizer;

use Apitizer\Types\Apidoc;
use Apitizer\Types\ApidocCollection;

class Apitizer
{
    public static function getQueryBuilderDocumentation(): ApidocCollection
    {
        $builders = collect(config('apitizer.query_builders', []))
                  ->map(function (string $builderClass) {
                      return new Apidoc(new $builderClass);
                  });

        return new ApidocCollection($builders);
    }

    public static function getFieldKey()
    {
        return config('apitizer.query_parameters.fields');
    }

    public static function getSortKey()
    {
        return config('apitizer.query_parameters.sort');
    }

    public static function getFilterKey()
    {
        return config('apitizer.query_parameters.filters');
    }
}
