<?php

namespace Apitizer;

use Apitizer\Types\ApidocCollection;
use Apitizer\Types\Apidoc;

class Apitizer
{
    /**
     * Get a list of fully-qualified class names.
     *
     * @return string[]
     */
    public static function getQueryBuilders(): array
    {
        return app(QueryBuilderLoader::class)->getQueryBuilders();
    }

    /**
     * Get the documentation for each registered query builder.
     *
     * @return ApidocCollection<Apidoc>
     */
    public static function getQueryBuilderDocumentation(): ApidocCollection
    {
        return ApidocCollection::forQueryBuilders(self::getQueryBuilders());
    }

    /**
     * Get the key that should be used in requests to fetch the fields.
     *
     * @return string
     */
    public static function getFieldKey(): string
    {
        return config('apitizer.query_parameters.fields');
    }

    /**
     * Get the key that should be used in requests to fetch the sorting.
     *
     * @return string
     */
    public static function getSortKey(): string
    {
        return config('apitizer.query_parameters.sort');
    }

    /**
     * Get the key that should be used in requests to fetch the filters.
     *
     * @return string
     */
    public static function getFilterKey(): string
    {
        return config('apitizer.query_parameters.filters');
    }

    /**
     * Get the key that should be used in requests to limit the count of items
     * returned in a paginated response.
     *
     * @return string
     */
    public static function getLimitKey(): string
    {
        return config('apitizer.query_parameters.limit');
    }

    /**
     * Get the mapping of all query params.
     *
     * @return array<string, string>
     *
     * @see Apitizer::getFieldKey
     * @see Apitizer::getSortKey
     * @see Apitizer::getFilterKey
     * @see Apitizer::getLimitKey
     */
    public static function getQueryParams(): array
    {
        return config('apitizer.query_parameters', []);
    }

    /**
     * Get the route to the documentation.
     *
     * If documentation generation is disabled, this will return null.
     *
     * @return null|string
     */
    public static function getRouteUrl(): ?string
    {
        if (! config('apitizer.generate_documentation', true)) {
            return null;
        }

        return config('apitizer.route_prefix');
    }
}
