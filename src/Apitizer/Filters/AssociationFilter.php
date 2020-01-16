<?php

namespace Apitizer\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class AssociationFilter
{
    /**
     * @var string The name of the relation on the query builder.
     */
    protected $relation;

    /**
     * @var string The column name to apply the filtering to.
     */
    protected $column;

    public function __construct(string $relation, string $column)
    {
        $this->relation = $relation;
        $this->column = $column;
    }

    public function __invoke(Builder $query, $values)
    {
        $values = Arr::wrap($values);
        $relation = $query->getModel()->{$this->relation}();

        if ($relation instanceof BelongsTo || $relation instanceof HasOneOrMany) {
            $this->applyFilter($query, $values, $relation);
        } else {
            $this->inefficientFilter($query, $values);
        }
    }

    protected function applyFilter(Builder $query, array $values, Relation $relation)
    {
        $table = $relation->getQuery()->getModel()->getTable();
        $localJoinKey = $this->getLocalJoinKey($relation);
        $foreignJoinKey = $this->getForeignJoinKey($relation);

        // Example: Tables "users" and "posts" with posts.author_id -> users.id.
        // If we filter from the "posts" on the author through the users.id,
        // then we don't need to use a subquery/join and we can instead just
        // use the posts.author_id directly.
        // From: select * from posts where author_id in (select id from users where id in (VALUES))
        // To  : select * from posts where author_id in (VALUES)
        if ($relation instanceof BelongsTo && $foreignJoinKey === $this->column) {
            $query->whereIn($localJoinKey, $values);
            return;
        }

        $query->whereIn($localJoinKey, function ($query) use ($values, $table, $foreignJoinKey) {
            $query->from($table)
                ->select($foreignJoinKey)
                ->whereIn($this->column, $values);
        });
    }

    /**
     * @param BelongsTo|HasOneOrMany $relation
     */
    protected function getLocalJoinKey(Relation $relation): string
    {
        return $relation instanceof BelongsTo
            ? $relation->getForeignKeyName()
            : $relation->getLocalKeyName();
    }

    /**
     * @param BelongsTo|HasOneOrMany $relation
     */
    protected function getForeignJoinKey(Relation $relation): string
    {
        return $relation instanceof BelongsTo
            ? $relation->getOwnerKeyName()
            : $relation->getForeignKeyName();
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
