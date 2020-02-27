<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class DigitsBetweenRule implements ValidationRule
{
    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function getName(): string
    {
        return 'digits_between';
    }

    public function getParameters(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
        ];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.digits_between', $this->getParameters());
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . $this->min . ',' . $this->max;
    }

    public function toHtml()
    {
        return trans('apitizer::validation.digits_between', [
            'min' => "<code>{$this->min}</code>",
            'max' => "<code>{$this->max}</code>",
        ]);
    }
}
