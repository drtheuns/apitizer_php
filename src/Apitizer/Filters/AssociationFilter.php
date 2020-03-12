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
     * @var string The name of the relation on the schema.
     */
    protected $relation;

    /**
     * @var null|string The column name to apply the filtering to.
     */
    protected $column;

    /**
     * @param string $relation the name of the relation on the model.
     * @param null|string $column the column name that should be compared. Defaults to
     * the related model's primary key.
     */
    public function __construct(string $relation, ?string $column = null)
    {
        $this->relation = $relation;
        $this->column = $column;
    }

    /**
     * @param Builder $query
     * @param string|string[] $values
     */
    public function __invoke(Builder $query, $values): void
    {
        $values = Arr::wrap($values);
        $relation = $query->getModel()->{$this->relation}();

        // Default to the primary key on the related table if no column was
        // given.
        $column = $this->column ?? $relation->getRelated()->getKeyName();

        if ($relation instanceof BelongsTo || $relation instanceof HasOneOrMany) {
            $this->applyFilter($query, $values, $relation, $column);
        } else {
            $this->inefficientFilter($query, $values, $column);
        }
    }

    /**
     * @param Builder $query
     * @param string[] $values
     * @param BelongsTo|HasOneOrMany $relation
     */
    protected function applyFilter(Builder $query, array $values, Relation $relation, string $column): void
    {
        /** @var \Illuminate\Database\Eloquent\Model */
        $model = $relation->getQuery()->getModel();
        $table = $model->getTable();
        $localJoinKey = $this->getLocalJoinKey($relation);
        $foreignJoinKey = $this->getForeignJoinKey($relation);

        // Example: Tables "users" and "posts" with posts.author_id -> users.id.
        // If we filter from the "posts" on the author through the users.id,
        // then we don't need to use a subquery/join and we can instead just
        // use the posts.author_id directly.
        // From: select * from posts where author_id in (select id from users where id in (VALUES))
        // To  : select * from posts where author_id in (VALUES)
        if ($relation instanceof BelongsTo && $foreignJoinKey === $column) {
            $query->whereIn($localJoinKey, $values);
            return;
        }

        $query->whereIn($localJoinKey, function ($query) use ($values, $table, $foreignJoinKey, $column) {
            $query->from($table)
                ->select($foreignJoinKey)
                ->whereIn($column, $values);
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

    /**
     * @param Builder $query
     * @param string[] $values
     */
    protected function inefficientFilter(Builder $query, array $values, string $column): void
    {
        // whereHas translates to a WHERE EXISTS which often results in a full
        // table scan (at least on MySQL).
        $query->whereHas($this->relation, function (Builder $query) use ($values, $column) {
            $query->whereIn($column, $values);
        });
    }
}
