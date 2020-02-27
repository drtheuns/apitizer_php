<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;
use Illuminate\Validation\Rules\RequiredIf;

class RequiredIfRule extends RequiredIf implements ValidationRule
{
    /**
     * @var string a short explanation of when this field is required.
     */
    protected $explanation;

    public function __construct($condition, string $explanation)
    {
        $this->condition = $condition;
        $this->explanation = $explanation;
    }

    public function getName(): string
    {
        return 'required_if';
    }

    public function getParameters(): array
    {
        return [];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.required_if', ['reason' => $this->explanation]);
    }

    public function toValidationRule()
    {
        return $this;
    }

    public function toHtml()
    {
        return $this->getDocumentation();
    }
}
