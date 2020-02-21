<?php

namespace Apitizer\Validation;

/**
 * The interface that each rule must implement in order to be documentable.
 */
interface DocumentableRule
{
    /**
     * Get the description of this rule that will be shown in the documentation.
     *
     * Return null or false if this rule should not be documented.
     */
    public function getDescription(): ?string;
}
