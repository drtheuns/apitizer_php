<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class DateFormatRule implements ValidationRule
{
    /**
     * @var string
     */
    protected $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    public function getName(): string
    {
        return 'date_format';
    }

    public function getParameters(): array
    {
        return ['format' => $this->format];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.date_format', ['format' => $this->format]);
    }

    public function toValidationRule()
    {
        return $this->getName() . ":{$this->format}";
    }

    public function toHtml()
    {
        return trans('apitizer::validation.date_format', [
            'format' => "<code>{$this->format}</code>"
        ]);
    }
}
