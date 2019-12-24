<?php

namespace Apitizer\FieldTypes;

use Apitizer\FieldType;

abstract class BaseType implements FieldType
{
    /**
     * The key that this type has on the source data.
     *
     * In the case of the database, this key would be a column name on the
     * Eloquent model.
     *
     * @var string
     */
    protected $key;

    /**
     * The value that is held by the current type class.
     *
     * @var mixed
     */
    protected $value;

    /**
     * A transformation closure or class that is applied after rendering.
     *
     * @var null|Callable
     */
    protected $transformer = null;

    /**
     * {@inheritdoc}
     */
    abstract public function type(): string;

    /**
     * Given a value, cast it to the right type and dump it to the right
     * representation.
     */
    abstract public function cast($value);

    /**
     * {@inheritdoc}
     */
    public function render($value)
    {
        $value = $this->cast($value);

        return $this->transformer
            ? $this->transformer($value)
            : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function transformedBy(callable $transformer): FieldType
    {
        $this->transformer = $transformer;

        return $this;
    }

    public function setKey(string $key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }
}
