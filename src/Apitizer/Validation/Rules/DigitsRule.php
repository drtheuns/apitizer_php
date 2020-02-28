<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class DigitsRule implements ValidationRule
{
    /**
     * @var int
     */
    protected $digits;

    public function __construct(int $digits)
    {
        $this->digits = $digits;
    }

    public function getName(): string
    {
        return 'digits';
    }

    public function getParameters(): array
    {
        return ['digits' => $this->digits];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.digits', ['length' => $this->digits]);
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . $this->digits;
    }

    public function toHtml(): string
    {
        return $this->getDocumentation() ?? '';
    }
}
