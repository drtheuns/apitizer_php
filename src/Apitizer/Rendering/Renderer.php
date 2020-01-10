<?php

namespace Apitizer\Rendering;

use Apitizer\QueryBuilder;
use Apitizer\Types\Association;
use Apitizer\Types\Field;

/**
 * Describes a class that can render data for the query builder.
 */
interface Renderer
{
    /**
     * Render data that was fetched according to the fetch specification.
     *
     * @param QueryBuilder $queryBuilder
     * @param array|Collection|object|iterable $data
     * @param (Field|Association)[] $selectedFields
     * @return array
     */
    public function render(QueryBuilder $queryBuilder, $data, array $selectedFields): array;
}
