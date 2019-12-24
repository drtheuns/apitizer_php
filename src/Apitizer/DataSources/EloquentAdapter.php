<?php

namespace Apitizer\DataSources;

use Apitizer\QueryBuilder;
use Apitizer\QueryBuilder\Field;
use Apitizer\QueryableDataSource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class EloquentAdapter implements QueryableDataSource
{
    /** @var Builder */
    protected $query;

    /** @var QueryBuilder */
    protected $queryBuilder;

    public function setQueryBuilder(QueryBuilder $queryBuilder): QueryableDataSource
    {
        $this->queryBuilder = $queryBuilder;
        $this->query = $queryBuilder->datasource();

        return $this;
    }

    public function applyFilters(array $filters): QueryableDataSource
    {
        return $this;
    }

    public function applySorting(array $sorts): QueryableDataSource
    {
        return $this;
    }

    public function applySelect(array $fields): QueryableDataSource
    {
        $this->doApplySelect($this->query, $fields);

        return $this;
    }

    private function doApplySelect($query, array $fields, array $additionalSelects = [])
    {
        // Always load the primary key in case there are relationships that
        // depend on it.
        $selectKeys = array_merge([$query->getModel()->getKeyName()], $additionalSelects);

        foreach ($fields as $fieldOrAssoc) {
            if ($fieldOrAssoc instanceof Field) {
                // Also load any of the selected keys.
                $selectKeys[] = $fieldOrAssoc->getType()->getKey();
            } else {
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

                        $this->doApplySelect(
                            $relation, $fieldOrAssoc->getFields(), $additionalSelects
                        );
                    }]
                );
            }
        }

        $query->select(array_unique($selectKeys));
    }

    public function fetchData(): iterable
    {
        return $this->query->get();
    }
}
