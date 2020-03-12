<?php

namespace Apitizer\Rendering;

use Apitizer\Schema;
use Apitizer\Types\FetchSpec;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Describes a class that can render data for the schema.
 */
interface Renderer
{
    /**
     * Render data that was fetched according to the fetch specification.
     *
     * @param Schema $schema
     * @param array<mixed>|Collection|object|iterable<mixed> $data
     * @param FetchSpec $fetchSpec
     * @return array<string, mixed>|array<int, array<string, mixed>>
     */
    public function render(Schema $schema, $data, FetchSpec $fetchSpec): array;

    /**
     * Render a paginated response
     *
     * @param Schema $schema
     * @param LengthAwarePaginator $paginator
     * @param FetchSpec $fetchSpec
     *
     * @return array<string, mixed>|LengthAwarePaginator
     */
    public function paginate(Schema $schema, LengthAwarePaginator $paginator, FetchSpec $fetchSpec);
}
