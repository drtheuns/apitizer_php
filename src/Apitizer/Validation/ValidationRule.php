<?php

namespace Apitizer\Validation;

use Illuminate\Contracts\Validation\Rule;

/**
 * The interface that every rule must implement.
 *
 * The goal of this interface is to allow programmatic access to most aspects of
 * the rules, while also prioritizing documentation.
 */
interface ValidationRule
{
    /**
     * Get the name of this validation rule.
     */
    public function getName(): string;

    /**
     * Get the documentation for this rule.
     *
     * If null is returned, the documentation will not be displayed.
     */
    public function getDocumentation(): ?string;

    /**
     * Get the parameters for this rule.
     *
     * The keys of this array should be appropriately named.
     */
    public function getParameters(): array;

    /**
     * Convert the rule to a validation rule that Laravel's Validator can
     * understand.
     *
     * @return string|Rule
     */
    public function toValidationRule();

    /**
     * Renders the HTML that will be used by the generated documentation.
     *
     * This should be kept to a minimum to ensure maximum compatibility with
     * custom rendered documentation pages. It should therefore not contain
     * anything other than plain inline-tags without additional attributes.
     */
    public function toHtml();
}
