<?php

namespace Apitizer\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class AssociationFilter
{
    /**
     * The name of the relation on the query builder.
     *
     * @var string
     */
    protected $relation;

    /**
     * The column name to apply the filtering to.
     *
     * @var string
     */
    protected $column;

    public function __construct(string $relation, string $column)
    {
        $this->relation = $relation;
        $this->column = $column;
    }

    public function __invoke(Builder $query, array $values)
    {
        $relation = $query->getModel()->{$this->relation}();

        if (! $relation instanceof BelongsTo && ! $relation instanceof HasOneOrMany) {
            return $this->inefficientFilter($query, $values);
        }

        $table = $relation->getQuery()->getModel()->getTable();
        $relatedKey = $relation instanceof HasOneOrMany
                    ? $relation->getForeignKeyName()
                    : $relation->getForeignKey();

        $query->whereIn('id', function ($query) use ($values, $table, $relatedKey) {
            $query->from($table)
                  ->select($relatedKey)
                  ->whereIn($this->column, $values);
        });
    }

    protected function inefficientFilter(Builder $query, array $values)
    {
        // whereHas translates to a WHERE EXISTS which often results in a full
        // table scan (at least on MySQL).
        return $query->whereHas($this->relation, function (Builder $query) use ($values) {
            $query->whereIn($this->column, $values);
        });
    }
}
