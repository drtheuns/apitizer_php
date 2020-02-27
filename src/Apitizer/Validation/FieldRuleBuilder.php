<?php

namespace Apitizer\Validation;

use Apitizer\Validation\Rules\Constraint;
use Illuminate\Contracts\Validation\Rule;

abstract class FieldRuleBuilder implements TypedRuleBuilder
{
    /**
     * @var string|null
     */
    protected $fieldName;

    /**
     * @var (ValidationRule|Rule)[] the validation rules that should apply to
     * this field.
     */
    protected $rules = [];

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @var bool
     */
    protected $nullable = false;

    /**
     * @var string|null
     */
    protected $prefix;

    public function __construct(?string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * Get the type of the current field.
     */
    abstract public function getType(): string;

    public function addRule($rule): self
    {
        $this->rules[] = $rule;

        return $this;
    }

    protected function addConstraint(string $name): self
    {
        return $this->addRule(new Constraint($name));
    }

    /**
     * Get the name of the current field.
     */
    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @internal
     */
    public function getValidatableType()
    {
        return $this->getType();
    }

    /**
     * @internal
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getValidationRuleName(): string
    {
        return $this->prefix . $this->getFieldName();
    }
}
