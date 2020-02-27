<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class EmailRule implements ValidationRule
{
    /**
     * @var array
     */
    protected $styles = [];

    public function __construct(array $styles)
    {
        $this->styles = $styles;
    }

    public function getName(): string
    {
        return 'email';
    }

    public function getParameters(): array
    {
        return ['styles' => $this->styles];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.email');
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . implode(',', $this->styles);
    }

    public function toHtml()
    {
        return $this->getDocumentation();
    }
}
