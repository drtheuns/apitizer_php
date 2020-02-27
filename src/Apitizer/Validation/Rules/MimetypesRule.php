<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class MimetypesRule implements ValidationRule
{
    /**
     * @var string[]
     */
    protected $mimetypes;

    public function __construct(array $mimetypes)
    {
        $this->mimetypes = $mimetypes;
    }

    public function getName(): string
    {
        return 'mimetypes';
    }

    public function getParameters(): array
    {
        return ['mimetypes' => $this->mimetypes];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.mimetypes');
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . implode(',', $this->mimetypes);
    }

    public function toHtml()
    {
        $values = collect($this->mimetypes)->map(function ($value) {
            return '<code>' . $value . '</code>';
        })->implode(', ');

        return $this->getDocumentation() . ': ' . $values;
    }
}
