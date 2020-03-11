<?php

namespace Apitizer\Rendering;

use Apitizer\QueryBuilder;
use Apitizer\Types\FetchSpec;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    /**
     * Render a paginated response
     *
     * @param QueryBuilder $queryBuilder
     * @param LengthAwarePaginator $paginator
     * @param FetchSpec $fetchSpec
     *
     * @return array<string, mixed>|LengthAwarePaginator
     */
    public function paginate(QueryBuilder $queryBuilder, LengthAwarePaginator $paginator, FetchSpec $fetchSpec);
}
