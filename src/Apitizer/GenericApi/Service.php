<?php

namespace Apitizer\GenericApi;

use Apitizer\QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * The service interface is used by the route provider to indicate the handler
 * for some request.
 */
interface Service
{
    /**
     * Stores a new model based on the validated attributes.
     *
     * @param array<string, mixed> $validated
     * @param QueryBuilder $queryBuilder
     *
     * @return Model
     */
    public function create(array $validated, QueryBuilder $queryBuilder): Model;

    /**
     * @param Model $model
     * @param array<string, mixed> $validated
     * @param QueryBuilder $queryBuilder
     *
     * @return Model
     */
    public function update(Model $model, array $validated, QueryBuilder $queryBuilder): Model;

    /**
     * @param Model $model
     * @param QueryBuilder $queryBuilder
     *
     * @return void
     */
    public function delete(Model $model, QueryBuilder $queryBuilder): void;
}
