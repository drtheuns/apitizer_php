<?php

namespace Apitizer;

/**
 * A field type determines how some field should be rendered, which
 * transformations must be applied to it, and, as the name implies, which type
 * some field has.
 *
 * This is especially handy when generating documentation for your API.
 *
 * @see Apitizer\FieldTypes\AnyType for the default implementation.
 */
interface FieldType
{
    /**
     * Render the value to the correct representation for the final output.
     */
    public function render($value);

    /**
     * Get the name of the current type.
     *
     * This is used for documentation purposes.
     */
    public function type(): string;

    /**
     * Builder method to set the transformation that should be applied before
     * rendering.
     */
    public function transformedBy(callable $transformer): FieldType;
}
