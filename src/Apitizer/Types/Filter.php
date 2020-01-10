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

    public function expect(string $type): self
    {
        $this->expectArray = false;
        $this->type = $type;

        return $this;
    }

    public function expectMany(string $type): self
    {
        $this->expectArray = true;
        $this->type = $type;

        return $this;
    }

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
        // Default to the model's primary key.
        $key = $key ?? $this->queryBuilder->model()->getKeyName();

        $this->handleUsing(new AssociationFilter($relation, $key));

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
                return TypeCaster::cast($value, $this->type);
            }, $input);
        }

        if (\is_array($input)) {
            throw InvalidInputException::filterTypeError($this, $input);
        }

        return TypeCaster::cast($input, $this->type);
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

    public function getInputType(): string
    {
        return $this->expectArray
            ? "array of {$this->type}"
            : $this->type;
    }
}
