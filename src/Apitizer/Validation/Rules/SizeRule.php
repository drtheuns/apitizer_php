<?php

namespace Apitizer\Validation\Rules;

use Apitizer\Validation\ValidationRule;

class SizeRule implements ValidationRule
{
    /**
     * @var int|float
     */
    protected $size;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param int|float $size
     * @param string $type
     */
    public function __construct($size, $type)
    {
        $this->size = $size;
        $this->type = $type;
    }

    public function getName(): string
    {
        return 'size';
    }

    public function getParameters(): array
    {
        return [
            'size' => $this->size,
        ];
    }

    public function getDocumentation(): ?string
    {
        return trans('apitizer::validation.size', [
            'size' => $this->suffixUnit($this->size)
        ]);
    }

    public function toValidationRule()
    {
        return $this->getName() . ':' . $this->size;
    }

    public function toHtml(): string
    {
        return trans('apitizer::validation.size', [
            'size' => "<code>{$this->suffixUnit($this->size)}</code>"
        ]);
    }

    /**
     * @param int|float $value
     */
    protected function suffixUnit($value): string
    {
        $suffix = '';

        switch ($this->type) {
            case 'string':
                $suffix = $value === 1 ? 'character' : 'characters';
                break;
            case 'file':
                $suffix = $value === 1 ? 'byte' : 'bytes';
                break;
            case 'array':
                $suffix = $value === 1 ? 'element' : 'elements';
        }

        $value = (string) $value;

        return empty($suffix) ? $value : "$value $suffix";
    }
}
