<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\DocumentableRule;
use Apitizer\Validation\ValidationRule;

/**
 * Container to hold simple validation rules so we don't have to implement
 * a class for each one.
 */
class SimpleRule implements DocumentableRule, ValidationRule
{
    /**
     * @var string
     */
    protected $rule;

    /**
     * @var array|null
     */
    protected $parameters;

    /**
     * @var string|null
     */
    protected $description;

    public function __construct($rule, array $parameters = null, string $description = null)
    {
        $this->rule = $rule;
        $this->parameters = $parameters;
        $this->description = $description;
    }

    public function toValidationRule()
    {
        if ($this->parameters) {
            return $this->rule . ':' . implode(',', $this->parameters);
        }

        return $this->rule;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getRule(): string
    {
        return $this->rule;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
