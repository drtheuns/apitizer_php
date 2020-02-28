<?php

namespace Apitizer\Validation\Rules;

class MaxRule extends SizeRule
{
    public function getName(): string
    {
        return 'max';
    }

    public function getParameters(): array
    {
        return ['max' => $this->size];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.max', [
            'max' => $this->suffixUnit($this->size),
        ]);
    }

    public function toHtml(): string
    {
        return trans('apitizer::validation.max', [
            'max' => "<code>{$this->suffixUnit($this->size)}</code>"
        ]);
    }
}
