<?php

namespace Apitizer\Types;

use ArrayAccess;

class Field
{
    use Concerns\HasDescription;

    /**
     * The name of the field that the client uses.
     *
     * @var string
     */
    protected $name;

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

    public function __construct(string $key, string $type, string $name = null)
    {
        $this->key = $key;
        $this->type = $type;
        $this->name = $name;
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

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
