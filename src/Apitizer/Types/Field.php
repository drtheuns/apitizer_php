<?php

namespace Apitizer\Types;

use Apitizer\Exceptions\CastException;
use Apitizer\Exceptions\InvalidInputException;
use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\QueryBuilder;
use Apitizer\Rendering\Renderer;
use ArrayAccess;

class Field extends Factory
{
    use RendersValues;

    /**
     * @var string The key that this field occupies on the data source.
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

    /**
     * Render a row of data using this field.
     *
     * @param ArrayAccess|array|object $row
     *
     * @throws InvalidInputException if the value does not adhere to the requirements set by the field.
     *         For example, if the field is not nullable but the value is null, this will throw an error.
     *         Enum field may also throw an error if the value is not in the enum.
     *
     * @return mixed
     */
    public function render($row)
    {
        $value = $this->validateValue(
            $this->valueFromRow($row, $this->getKey()), $row
        );

        foreach ($this->transformers as $transformer) {
            try {
                $value = call_user_func($transformer, $value, $this);
            } catch (CastException $e) {
                $e = InvalidOutputException::castError($this, $e, $row);
                $this->getQueryBuilder()->handleException($e);

                // If the error is ignored, continuing transformations will likely
                // generate more unexpected errors.
                $value = null;
                break;
            }
        }

        return $value;
    }

    protected function validateValue($value, $row)
    {
        if (is_null($value) && !$this->isNullable()) {
            throw InvalidOutputException::fieldIsNull($this, $row);
        }

        return $value;
    }

    /**
     * @return $this
     */
    public function transform(callable $callable): self
    {
        $this->transformers[] = $callable;

        return $this;
    }

    /**
     * @return $this
     */
    public function nullable(bool $isNullable = true): self
    {
        $this->nullable = $isNullable;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Used when printing the api documentation.
     *
     * This is a separate function to allow specialized field types from having
     * deviating types vs how they are displayed, such as enums.
     */
    public function printType(): string
    {
        return $this->typeOrNull($this->getType());
    }

    protected function typeOrNull(string $type): string
    {
        return $this->isNullable()
            ? "$type or null"
            : $type;
    }
}
