<?php

namespace Apitizer\Interpreter;

use Apitizer\Types\FetchSpec;
use Apitizer\QueryBuilder;
use Apitizer\Types\Field;
use Apitizer\Types\Association;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class QueryInterpreter
{
    public function build(QueryBuilder $queryBuilder, FetchSpec $fetchSpec): Builder
    {
        $query = $queryBuilder->model()->query();
        $query = $queryBuilder->beforeQuery($query, $fetchSpec);

        $this->applySelect(
            $query, $fetchSpec->getFields(), $queryBuilder->getAlwaysLoadColumns()
        );
        $this->applySorting($query, $fetchSpec->getSorts());
        $this->applyFilters($query, $fetchSpec->getFilters());

        $query = $queryBuilder->afterQuery($query, $fetchSpec);

        return $query;
    }

    private function applySelect(Builder $query, array $fields, array $additionalSelects = [])
    {
        // Always load the primary key in case there are relationships that
        // depend on it.
        $selectKeys = array_merge([$query->getModel()->getKeyName()], $additionalSelects);

        foreach ($fields as $fieldOrAssoc) {
            if ($fieldOrAssoc instanceof Field) {
                // Also load any of the selected keys.
                $selectKeys[] = $fieldOrAssoc->getKey();
            } else if ($fieldOrAssoc instanceof Association) {
                // We also need to ensure that we always load the right foreign
                // keys, otherwise we won't be able load relationships.
                $relationship = $query->getModel()->{$fieldOrAssoc->getKey()}();

                // Perhaps we could even eager load belongsTo relationships
                // in-line using a join and table aliases, since there's always
                // only one related row in a belongsTo.
                if ($relationship instanceof BelongsTo) {
                    $selectKeys[] = $relationship->getForeignKeyName();
                }

                // Finally, we'll recursively eager load relationships with
                // efficient selects on those models as well.
                $query->with([
                    $fieldOrAssoc->getKey() => function ($relation) use ($fieldOrAssoc) {
                        // Similar to the BelongsTo above, we need to select the
                        // foreign key on the related model, otherwise Eloquent
                        // won't be able to piece things back together again.
                        $additionalSelects = $relation instanceof HasOneOrMany
                                           ? [$relation->getForeignKeyName()]
                                           : [];

                        $additionalSelects = array_merge(
                            $additionalSelects,
                            $fieldOrAssoc->getRelatedQueryBuilder()->getAlwaysLoadColumns()
                        );

                        $this->applySelect(
                            $relation->getQuery(),
                            $fieldOrAssoc->getFields(),
                            $additionalSelects
                        );
                    }]
                );
            }
        }

        $query->select(array_unique($selectKeys));
    }

    private function applySorting(Builder $query, array $sorts)
    {
        foreach ($sorts as $sort) {
            if ($handler = $sort->getHandler()) {
                call_user_func($handler, $query, $sort);
            }
        }
    }

    private function applyFilters(Builder $query, array $filters)
    {
        foreach ($filters as $filter) {
            if ($handler = $filter->getHandler()) {
                call_user_func($handler, $query, $filter->getValue());
            }
        }
    }
}
