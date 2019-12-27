<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use ArrayAccess;

class Association
{
    use Concerns\HasDescription;

    /**
     * The key of this association on the data source.
     *
     * @var string
     */
    protected $key;

    /**
     * The name of the field that the client uses.
     *
     * @var string
     */
    protected $name;

    /**
     * The query builder that is responsible for rendering this relationship.
     *
     * @var QueryBuilder
     */
    protected $builder;

    /**
     * The fields to render on the related builder.
     */
    protected $fields;

    public function __construct(string $key, QueryBuilder $builder)
    {
        $this->key = $key;
        $this->builder = $builder;
    }

    public function render(ArrayAccess $row)
    {
        return $this->builder->transformValues($row[$this->key], $this->fields);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getQueryBuilder()
    {
        return $this->builder;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    /**
     * Check if this association returns a collection of related rows.
     */
    public function returnsCollection()
    {
        $model = $this->builder->getParent()->model();
        $relation = $model->{$this->key}();

        return ! $relation instanceof BelongsTo && ! $relation instanceof HasOne;
    }
}
