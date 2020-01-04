<?php

namespace Apitizer\Types;

use Apitizer\Support\TypeCaster;
use Apitizer\Filters\LikeFilter;
use Illuminate\Database\Eloquent\Builder;

class Filter extends Factory
{
    /**
     * The type of value(s) to expect.
     *
     * @var string
     */
    protected $type = 'string';

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

    /**
     * @var mixed
     */
    protected $value = null;

    public function expect(string $type)
    {
        $this->expectArray = false;
        $this->type = $type;

        return $this;
    }

    public function expectMany(string $type)
    {
        $this->expectArray = true;
        $this->type = $type;

        return $this;
    }

    public function handleUsing(callable $handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Filter by field and operator.
     *
     * When this is method is used, expectMany cannot be used.
     *
     * @param string $field
     * @param string $operator
     *
     * @return self
     */
    public function byField(string $field, string $operator = '='): self
    {
        $this->expectArray = false;

        $this->handleUsing(function (Builder $query, $value) use ($field, $operator) {
            return $query->where($field, $operator, $value);
        });

        return $this;
    }

    /**
     * Filter using a LIKE filter on the given field(s).
     *
     * When this is method is used, expectMany cannot be used.
     *
     * @param array|string $fields
     *
     * @return self
     */
    public function search($fields): self
    {
        $this->expect('string');
        $this->handleUsing(new LikeFilter($fields));
        $this->description('Search based on the input string');

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
        if ($this->expectArray) {
            if (! \is_array($input)) {
                throw new \UnexpectedValueException();
            }

            return array_map(function ($value) {
                return TypeCaster::cast($value, $this->type);
            }, $input);
        }

        if (\is_array($input)) {
            throw new \UnexpectedValueException();
        }

        return TypeCaster::cast($input, $this->type);
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getInputType()
    {
        return $this->expectArray
            ? "array of {$this->type}"
            : $this->type;
    }
}
