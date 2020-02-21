<?php

namespace Apitizer\Validation;

interface ValidationRule
{
    /**
     * The callback that returns a rule that Laravel's Validator can interpret.
     *
     * @return \Illuminate\Contracts\Validation\Rule|string
     */
    public function toValidationRule();

    /**
     * @return string
     */
    public function getRule(): string;

    /**
     * @return array|null
     */
    public function getParameters();
}
