<?php

namespace Apitizer\Types;

use Apitizer\Exceptions\CastException;
use Apitizer\Exceptions\InvalidInputException;
use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\Policies\PolicyFailed;
use Apitizer\QueryBuilder;
use Apitizer\Rendering\Renderer;
use ArrayAccess;

abstract class AbstractField extends Factory
{
    use Concerns\FetchesValueFromRow,
        Concerns\HasPolicy;

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

    /**
     * A callback that fetches the value for the current field during the
     * rendering process. For fields that use the Eloquent model, this function
     * would get the value at some key from the model.
     *
     * @param ArrayAccess|array<mixed>|object $row
     *
     * @return mixed the value that should be rendered.
     */
    abstract protected function getValue($row);

    /**
     * Add a transformation function that will be applied when rendering the
     * value. Transformers are applied in insertion order.
     *
     * @param callable $callable The transformation function. This will receive
     * three parameters:
     * 1. The value that should be transformed.
     * 2. The entire row that is currently being transformed.
     * 3. The Field instance (this object).
     *
     * @return $this
     */
    public function transform(callable $callable): self
    {
        $this->transformers[] = $callable;

        return $this;
    }

    /**
     * Set the field to nullable.
     *
     * @param bool $isNullable
     *
     * @return $this
     */
    public function nullable(bool $isNullable = true): self
    {
        $this->nullable = $isNullable;

        return $this;
    }

    /**
     * Render a row of data.
     *
     * @param ArrayAccess|array<mixed>|object $row
     *
     * @throws InvalidOutputException if the value does not adhere to the
     *         requirements set by the field. For example, if the field is not
     *         nullable but the value is null, this will throw an error. Enum
     *         field may also throw an error if the value is not in the enum.
     *
     * @return mixed the transformed value.
     */
    public function render($row)
    {
        $value = $this->validateValue($this->getValue($row), $row);

        if (! $this->passesPolicy($value, $row)) {
            return new PolicyFailed;
        }

        return $this->applyTransformers($value, $row);
    }

    /**
     * Apply all the transformers in insertion order.
     *
     * @param mixed $value the value to transform.
     * @param ArrayAccess|array<mixed>|object $row
     *
     * @return mixed the transformed value
     */
    protected function applyTransformers($value, $row)
    {
        foreach ($this->transformers as $transformer) {
            try {
                $value = call_user_func($transformer, $value, $row, $this);
            } catch (CastException $e) {
                $e = InvalidOutputException::castError($this, $e, $row);
                $this->getQueryBuilder()->handleException($e);

                // If the error is ignored, continuing transformations will likely
                // generate more unexpected errors, so we'll stop here.
                $value = null;
                break;
            }
        }

        return $value;
    }

    /**
     * Validate that the value from the row is valid according to the current
     * field type.
     *
     * @param mixed $value
     * @param mixed $row
     *
     * @throws InvalidOutputException
     *
     * @return mixed the value if it was valid.
     */
    public function validateValue($value, $row)
    {
        if (is_null($value) && !$this->isNullable()) {
            throw InvalidOutputException::fieldIsNull($this, $row);
        }

        return $value;
    }

    public function getType(): string
    {
        return $this->type;
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
