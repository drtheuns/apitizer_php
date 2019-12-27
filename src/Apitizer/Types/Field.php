<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;
use ArrayAccess;

class Field extends Factory
{
    /**
     * The key that this field occupies on the data source.
     */
    protected $key;

    /**
     * The internal type that is used for this field.
     *
     * @var string
     */
    protected $type;

    /**
     * Whether or not this field can be null.
     *
     * @var bool
     */
    protected $nullable = false;

    /**
     * The transformation callables that are called when the field is rendered.
     *
     * @var callable[]
     */
    protected $transformers = [];

    public function __construct(
        QueryBuilder $queryBuilder,
        string $key,
        string $type
    ) {
        parent::__construct($queryBuilder);
        $this->key = $key;
        $this->type = $type;
    }

    public function render(ArrayAccess $row)
    {
        $value = $row[$this->getKey()];

        foreach ($this->transformers as $transformer) {
            $value = $transformer($value, $this);
        }

        return $value;
    }

    public function transform(callable $callable): self
    {
        $this->transformers[] = $callable;

        return $this;
    }

    public function nullable(bool $isNullable = true): self
    {
        $this->nullable = $isNullable;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function isNullable()
    {
        return $this->nullable;
    }
}
