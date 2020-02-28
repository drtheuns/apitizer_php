<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class Constraint implements ValidationRule
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.'. $this->getName());
    }

    public function getParameters(): array
    {
        return [];
    }

    public function toValidationRule()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->getDocumentation() ?? '';
    }

    public function toHtml(): string
    {
        return (string) $this;
    }
}
