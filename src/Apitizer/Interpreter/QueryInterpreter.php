<?php

namespace Apitizer\Interpreter;

use Apitizer\Types\FetchSpec;
use Apitizer\QueryBuilder;
use Apitizer\Types\Field;
use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Types\Filter;
use Apitizer\Types\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class QueryInterpreter
{
    /**
     * Prepare the query based on the fetch specification.
     *
     * This will apply selects, filters, and sorting. The `beforeQuery` and
     * `afterQuery` hooks are called on the query builder before and after the
     * query is built.
     */
    public function build(QueryBuilder $queryBuilder, FetchSpec $fetchSpec): Builder
    {
        $query = $this->newQueryInstance($queryBuilder);
        $query = $queryBuilder->beforeQuery($query, $fetchSpec);

        $this->applySelect(
            $query,
            $fetchSpec->getFields(),
            $fetchSpec->getAssociations(),
            $queryBuilder->getAlwaysLoadColumns()
        );
        $this->applySorting($query, $fetchSpec->getSorts());
        $this->applyFilters($query, $fetchSpec->getFilters());

        $query = $queryBuilder->afterQuery($query, $fetchSpec);

        return $query;
    }

    protected function newQueryInstance(QueryBuilder $queryBuilder): Builder
    {
        return $queryBuilder->model()->query();
    }

    /**
     * @param Builder $query
     * @param AbstractField[] $fields
     * @param Association[] $associations
     * @param string[] $additionalSelects
     */
    protected function applySelect(
        Builder $query,
        array $fields,
        array $associations,
        array $additionalSelects = []
    ): void {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $query->getModel();

        // Always load the primary key in case there are relationships that
        // depend on it.
        $selectKeys = array_merge([$model->getKeyName()], $additionalSelects);

        foreach ($fields as $field) {
            // Generated fields don't select anything.
            if ($field instanceof Field) {
                $selectKeys[] = $field->getKey();
            }
        }

        foreach ($associations as $association) {
            $relationship = $model->{$association->getKey()}();

            // Ensure that we always load the correct foreign key, otherwise
            // Eloquent won't be able to load relationships.
            if ($relationship instanceof BelongsTo) {
                $selectKeys[] = $relationship->getForeignKeyName();
            }

            // Finally, recursively eager load relationships with efficient
            // selects on those queries as well.
            $query->with([
                $association->getKey() => function ($relation) use ($association) {
                    // Similar to the BelongsTo above, we need to select the
                    // foreign key on the related model, otherwise Eloquent
                    // won't be able to piece things back together again.
                    $additionalSelects = $relation instanceof HasOneOrMany
                                       ? [$relation->getForeignKeyName()]
                                       : [];

                    $additionalSelects = array_merge(
                        $additionalSelects,
                        $association->getRelatedQueryBuilder()->getAlwaysLoadColumns()
                    );

                    $this->applySelect(
                        $relation->getQuery(),
                        $association->getFields() ?? [],
                        $association->getAssociations() ?? [],
                        $additionalSelects
                    );
                }
            ]);
        }

        $query->select(array_unique($selectKeys));
    }

    /**
     * @param Builder $query
     * @param Sort[] $sorts
     */
    protected function applySorting(Builder $query, array $sorts): void
    {
        foreach ($sorts as $sort) {
            if ($handler = $sort->getHandler()) {
                call_user_func($handler, $query, $sort);
            }
        }
    }

    /**
     * @param Builder $query
     * @param Filter[] $filters
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $filter) {
            if ($handler = $filter->getHandler()) {
                call_user_func($handler, $query, $filter->getValue());
            }
        }
    }
}
