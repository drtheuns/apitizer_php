<?php

namespace Apitizer;

use Apitizer\Types\ApidocCollection;

class Apitizer
{
    public static function getQueryBuilderDocumentation(): ApidocCollection
    {
        return ApidocCollection::forQueryBuilders(
            config('apitizer.query_builders', [])
        );
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

    public static function getLimitKey()
    {
        return config('apitizer.query_parameters.limit');
    }

    public static function getQueryParams()
    {
        return array_values(config('apitizer.query_parameters', []));
    }

    public static function getRouteUrl(): ?string
    {
        if (! config('apitizer.generate_documentation', true)) {
            return null;
        }

        return config('apitizer.route_prefix');
    }
}
