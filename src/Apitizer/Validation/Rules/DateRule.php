<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;
use DateTimeInterface;

abstract class DateRule implements ValidationRule
{
    /**
     * @var DateTimeInterface
     */
    protected $date;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $formatted;

    public function __construct(DateTimeInterface $date, string $format)
    {
        $this->date = $date;
        $this->format = $format;
        $this->formatted = $date->format($format);
    }

    public function getParameters(): array
    {
        return [
            'date' => $this->date,
            'format' => $this->format,
        ];
    }

    public function getDocumentation(): ?string
    {
        return trans("apitizer::validation.{$this->getName()}", [
            'date' => $this->formatted,
        ]);
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . $this->formatted;
    }

    public function toHtml()
    {
        return trans("apitizer::validation.{$this->getName()}", [
            'date' => "<code>{$this->formatted}</code>",
        ]);
    }
}
