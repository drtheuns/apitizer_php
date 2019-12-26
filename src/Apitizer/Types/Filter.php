<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;
use Apitizer\Support\TypeCaster;

class Filter
{
    /**
     * The name of this filter that is available to clients.
     *
     * @var string
     */
    protected $name;

    /**
     * The query builder that created this filter.
     *
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * The type of value(s) to expect.
     *
     * @var string
     */
    protected $type;

    /**
     * If we expect an array of values or just one.
     *
     * @var bool
     */
    protected $expectArray = false;

    /**
     * @var callable
     */
    protected $handler = null;

    protected $value = null;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function expect(string $type)
    {
        $this->type = $type;

        return $this;
    }

    public function expectMany(string $type)
    {
        $this->type = $type;
        $this->expectArray = true;

        return $this;
    }

    public function handleUsing(callable $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Get the validated, type casted input.
     *
     * @throws \UnexpectedValueException
     *
     * @return array|mixed
     */
    public function validateInput($input)
    {
        if ($this->expectArray && ! is_array($input)) {
            throw new \UnexpectedValueException();
        }

        return $this->expectArray
            ? array_map(function ($value) {
                return TypeCaster::cast($value, $this->type);
            }, $input)
            : TypeCaster::cast($input);
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getHandler()
    {
        return $this->handler ?? $this->getDefaultHandler();
    }

    protected function getDefaultHandler()
    {
        // Check if the key is set and filter by key.eq.values
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}
