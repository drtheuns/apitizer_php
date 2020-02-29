<?php

namespace Apitizer\Rendering;

use Apitizer\QueryBuilder;
use Apitizer\Types\FetchSpec;

/**
 * Describes a class that can render data for the query builder.
 */
interface Renderer
{
    /**
     * Render data that was fetched according to the fetch specification.
     *
     * @param QueryBuilder $queryBuilder
     * @param array<mixed>|Collection|object|iterable<mixed> $data
     * @param FetchSpec $fetchSpec
     * @return array<string, mixed>|array<int, array<string, mixed>>
     */
    public function render(QueryBuilder $queryBuilder, $data, FetchSpec $fetchSpec): array;
}
