<?php

namespace Apitizer\Validation;

class ArrayRules extends FieldRuleBuilder implements ContainerType
{
    use Concerns\SharedRules;

    /**
     * @var null|FieldRuleBuilder
     */
    protected $elementType;

    public function distinct(): self
    {
        return $this->addConstraint('distinct');
    }

    /**
     * Specify the type for the values of the array.
     *
     * Any rules for the array field specifically needs to be defined before this
     * is called.
     */
    public function whereEach(): ArrayTypePicker
    {
        return new ArrayTypePicker($this);
    }

    /**
     * @internal
     */
    public function setElementType(TypedRuleBuilder $elementType)
    {
        $elementType->setPrefix($this->getRulePrefix());

        $this->elementType = $elementType;
    }

    public function getElementType(): ?TypedRuleBuilder
    {
        return $this->elementType;
    }

    public function getType(): string
    {
        return $this->elementType
            ? 'array of ' . $this->elementType->getType()
            : 'array';
    }

    public function getValidatableType()
    {
        return 'array';
    }

    public function getChildren(): array
    {
        return $this->elementType ? [$this->elementType] : [];
    }

    public function getRulePrefix(): string
    {
        return $this->getValidationRuleName() . '.*';
    }

    public function resolve()
    {
        if ($this->elementType instanceof ContainerType) {
            $this->elementType->resolve();
        }
    }
}
