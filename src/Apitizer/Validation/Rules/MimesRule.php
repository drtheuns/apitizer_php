<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class MimesRule implements ValidationRule
{
    /**
     * @var string[]
     */
    protected $mimes;

    /**
     * @param string[] $mimes
     */
    public function __construct(array $mimes)
    {
        $this->mimes = $mimes;
    }

    public function getName(): string
    {
        return 'mimes';
    }

    public function getParameters(): array
    {
        return ['mimes' => $this->mimes];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.mimes');
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . implode(',', $this->mimes);
    }

    public function toHtml(): string
    {
        $values = collect($this->mimes)->map(function ($value) {
            return '<code>' . $value . '</code>';
        })->implode(', ');

        return $this->getDocumentation() . ': ' . $values;
    }
}
