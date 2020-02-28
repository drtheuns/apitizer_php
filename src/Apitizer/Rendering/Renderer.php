<?php

namespace Apitizer\Rendering;

use Apitizer\QueryBuilder;
use Apitizer\Types\Association;
use Apitizer\Types\AbstractField;

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
     * @param (AbstractField|Association)[] $selectedFields
     * @return array<string, mixed>|array<int, array<string, mixed>>
     */
    public function render(QueryBuilder $queryBuilder, $data, array $selectedFields): array;
}
