<?php

namespace Apitizer\Types;

use Apitizer\Exceptions\InvalidInputException;
use Apitizer\Exceptions\CastException;
use Apitizer\Filters\AssociationFilter;
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
     * @var string the format for date(time) types.
     */
    protected $format = null;

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

    /**
     * Set the expected input type.
     *
     * To expect an array of types, look at `expectMany`.
     *
     * @param string $type
     * @param null|string $format the format for date(time) value. Defaults to
     * 'Y-m-d' for dates, and 'Y-m-d H:i:s' for datetimes. This format is
     * ignored for any other type.
     *
     * @return $this
     */
    public function expect(string $type, string $format = null): self
    {
        $this->expectArray = false;
        $this->type = $type;
        $this->format = $format;

        return $this;
    }

    /**
     * Expect an array of the given type as input to the filter.
     *
     * @param string $type
     * @param null|string $format the format for date(time) value. Defaults to
     * 'Y-m-d' for dates, and 'Y-m-d H:i:s' for datetimes. This format is
     * ignored for any other type.
     *
     * @return $this
     */
    public function expectMany(string $type, string $format = null): self
    {
        $this->expectArray = true;
        $this->type = $type;
        $this->format = $format;

        return $this;
    }

    /**
     * Set the handler function to be used when applying the filter.
     *
     * The function will receive two arguments:
     * 1. The Eloquent Builder instance.
     * 2. The value that was passed in the filter, cast to the type that was
     * specified.
     * No return value is expected.
     *
     * @param callable $handler
     *
     * @return $this
     */
    public function handleUsing(callable $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Filter by field and operator.
     *
     * If `expectMany` is used, the operator will be ignored in favour of a
     * `whereIn` query.
     *
     * @param string $field
     * @param string $operator
     *
     * @return self
     */
    public function byField(string $field, string $operator = '='): self
    {
        $this->handleUsing(function (Builder $query, $value) use ($field, $operator) {
            return $this->expectArray
                ? $query->whereIn($field, $value)
                : $query->where($field, $operator, $value);
        });

        return $this;
    }

    /**
     * Filter by association.
     *
     * Uses the AssociationFilter class.
     *
     * @param string $relation the name of the relation on the parent model (the
     * model of the current query builder)
     * @param null|string $key the key on the child model that should be
     * filtered on. Defaults to the primary key of that model.
     *
     * @return self
     */
    public function byAssociation(string $relation, string $key = null): self
    {
        $this->handleUsing(new AssociationFilter($relation, $key));

        return $this;
    }

    /**
     * Filter using a LIKE filter on the given field(s).
     *
     * When this is method is used, expectMany cannot be used and a string will
     * automatically be expected.
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
     * @throws InvalidInputException
     * @throws CastException
     *
     * @return array|mixed
     */
    protected function validateInput($input)
    {
        if ($this->expectArray) {
            if (! \is_array($input)) {
                throw InvalidInputException::filterTypeError($this, $input);
            }

            return array_map(function ($value) {
                return TypeCaster::cast($value, $this->type, $this->format);
            }, $input);
        }

        if (\is_array($input)) {
            throw InvalidInputException::filterTypeError($this, $input);
        }

        return TypeCaster::cast($input, $this->type, $this->format);
    }

    public function getHandler(): ?callable
    {
        return $this->handler;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        try {
            $this->value = $this->validateInput($value);
        } catch (CastException $e) {
            throw InvalidInputException::filterTypeError($this, $value);
        }

        return $this;
    }

    /**
     * Get the expected input type. This is formatted for the documentation.
     */
    public function getInputType(): string
    {
        return $this->expectArray
            ? "array of {$this->type}"
            : $this->type;
    }
}
