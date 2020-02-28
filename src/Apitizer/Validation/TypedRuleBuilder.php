<?php

namespace Apitizer\Validation;

use Illuminate\Contracts\Validation\Rule;

interface TypedRuleBuilder
{
    /**
     * Get the type of the current rule builder.
     */
    public function getType(): string;

    /**
     * Get a validatable type for the current rule builder.
     *
     * @return string|Rule|null
     */
    public function getValidatableType();

    /**
     * Get all the rules that have been defined on this builder.
     *
     * @return (ValidationRule|Rule|string)[]
     */
    public function getRules(): array;

    /**
     * Get the name of the current field. This may be null because the top-level
     * object does not have a name.
     */
    public function getFieldName(): ?string;

    /**
     * Get the full path name to this field for the validation rules.
     */
    public function getValidationRuleName(): string;

    /**
     * Check if this field is required.
     */
    public function isRequired(): bool;

    /**
     * Check if this field is nullable.
     */
    public function isNullable(): bool;

    /**
     * Set the validation name prefix.
     */
    public function setPrefix(string $prefix): void;
}
