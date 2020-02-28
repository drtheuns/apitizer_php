<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class RequiredWithRule implements ValidationRule
{
    /**
     * @var string[]
     */
    protected $fields;

    /**
     * @param string[] $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function getName(): string
    {
        return 'required_with';
    }

    public function getParameters(): array
    {
        return ['fields' => $this->fields];
    }

    public function getDocumentation(): ?string
    {
        return trans("apitizer::validation.{$this->getName()}");
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . implode(',', $this->fields);
    }

    public function toHtml(): string
    {
        $values = collect($this->fields)->map(function ($field) {
            return "<code>$field</code>";
        })->implode(', ');

        return $this->getDocumentation() . ': ' . $values;
    }
}
