<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;
use Apitizer\Rendering\Renderer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Association extends Factory
{
    use RendersValues;

    /**
     * The key of this association on the data source.
     *
     * @var string
     */
    protected $key;

    /**
     * The fields to render on the related query builder.
     */
    protected $fields;

    public function __construct(QueryBuilder $queryBuilder, string $key)
    {
        parent::__construct($queryBuilder);
        $this->key = $key;
    }

    public function render($row, Renderer $renderer)
    {
        $assocData = $this->valueFromRow($row, $this->getKey());

        return $renderer->render($this->getQueryBuilder(), $assocData, $this->fields);
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
        $model = $this->getQueryBuilder()->getParent()->model();
        $relation = $model->{$this->key}();

        return ! $relation instanceof BelongsTo && ! $relation instanceof HasOne;
    }
}
