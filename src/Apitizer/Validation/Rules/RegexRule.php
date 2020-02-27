<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class RegexRule implements ValidationRule
{
    /**
     * @var string
     */
    protected $regex;

    public function __construct(string $regex)
    {
        $this->regex = trim($regex, '/');
    }

    public function getName(): string
    {
        return 'regex';
    }

    public function getParameters(): array
    {
        return ['regex' => $this->regex];
    }

    public function getDocumentation(): ?string
    {
        return trans("apitizer::validation.{$this->getName()}", $this->getParameters());
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . "/{$this->regex}/";
    }

    public function toHtml()
    {
        return trans("apitizer::validation.{$this->getName()}", [
            'regex' => "<code>{$this->regex}</code>",
        ]);
    }
}
