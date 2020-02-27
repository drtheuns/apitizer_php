<?php

namespace Apitizer\Validation\Rules;

class MinRule extends SizeRule
{
    public function getName(): string
    {
        return 'min';
    }

    public function getParameters(): array
    {
        return ['min' => $this->size];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.min', [
            'min' => $this->suffixUnit($this->size),
        ]);
    }

    public function toHtml()
    {
        return trans('apitizer::validation.min', [
            'min' => "<code>{$this->suffixUnit($this->size)}</code>"
        ]);
    }
}
