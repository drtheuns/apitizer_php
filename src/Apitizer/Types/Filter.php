<?php

namespace Apitizer\Types;

use Apitizer\Exceptions\DefinitionException;
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
     * @var string|null the format for date(time) types.
     */
    protected $format = null;

    /**
     * @var array<string> the available enumators.
     */
    protected $enums = null;

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
     * @return FilterTypePicker
     */
    public function expect(): FilterTypePicker
    {
        $this->expectArray = false;

        return new FilterTypePicker($this);
    }

    public function whereEach(): FilterTypePicker
    {
        if ($this->type != "array") {
            throw DefinitionException::filterExpectRequired($this->getQueryBuilder(), $this);
        }

        return new FilterTypePicker($this);
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
     * If an array of input is given, the operator will be ignored in favour of a
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
     * @param string[]|string $fields
     *
     * @return self
     */
    public function search($fields): self
    {
        $this->expect()->string();
        $this->handleUsing(new LikeFilter($fields));
        $this->description('Search based on the input string');

        return $this;
    }

    /**
     * Get the validated, type casted input.
     *
     * @param mixed $input
     *
     * @throws InvalidInputException
     * @throws CastException
     *
     * @return array<string|int|float|\DateTimeInterface|bool|mixed>|string|int|float|\DateTimeInterface|bool|mixed
     */
    protected function validateInput($input)
    {
        // Array-input
        if ($this->expectArray) {
            if (!\is_array($input)) {
                throw InvalidInputException::filterTypeError($this, $input);
            }

            if ($this->enums) {
                foreach ($input as $value) {
                    if (! in_array($value, $this->enums)) {
                        throw InvalidInputException::filterTypeError($this, $input);
                    }
                }
            }

            return \array_map(function ($value) {
                return TypeCaster::cast($value, $this->type, $this->format);
            }, $input);
        }

        // Non-array input
        if (\is_array($input)) {
            throw InvalidInputException::filterTypeError($this, $input);
        }

        if ($this->enums && ! in_array($input, $this->enums)) {
            throw InvalidInputException::filterTypeError($this, $input);
        }

        return TypeCaster::cast($input, $this->type, $this->format);
    }

    public function getHandler(): ?callable
    {
        return $this->handler;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
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
     * @internal used by FilterTypePicker to set the type of the filter,
     *
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @internal used by FilterTypePicker to set whether or not to expect an array,
     *
     * @param bool $expectArray
     * @return self
     */
    public function setExpectArray(bool $expectArray): self
    {
        $this->expectArray = $expectArray;
        return $this;
    }

    /**
     * @internal used by FilterTypePicker to set the formatting.
     *
     * @param string $format
     * @return self
     */
    public function setFormatting(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param array<string> $enums, This is used to check whether the value is an available option.
     */
    public function setEnumerators(array $enums): self
    {
        $this->enums = $enums;
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
