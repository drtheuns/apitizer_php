<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

/**
 * A rule that compares its own value to another field.
 */
abstract class FieldRule implements ValidationRule
{
    /**
     * @var string
     */
    protected $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function getParameters(): array
    {
        return ['field' => $this->field];
    }

    public function getDocumentation(): ?string
    {
        return trans("apitizer::validation.{$this->field}", $this->getParameters());
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . $this->field;
    }

    public function toHtml(): string
    {
        return trans("apitizer::validation.{$this->field}", [
            'field' => "<code>{$this->field}</code>",
        ]);
    }
}
