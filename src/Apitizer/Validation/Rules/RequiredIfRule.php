<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\DocumentableRule;
use Apitizer\Validation\ValidationRule;
use Illuminate\Validation\Rules\RequiredIf;

class RequiredIfRule extends RequiredIf implements DocumentableRule
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

    public function getDescription(): ?string
    {
        return trans('apitizer::validation.required_if', ['reason' => $explanation]);
    }
}
