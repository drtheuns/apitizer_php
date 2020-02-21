<?php

namespace Apitizer\Validation;

use Apitizer\Validation\Rules\DimensionsRule;

class FileRuleBuilder extends TypedRuleBuilder
{
    /**
     * Validate that the file is an image (jpeg, png, bmp, gif, svg, webp)
     */
    public function image(): self
    {
        return $this->addSimpleRule('image');
    }

    /**
     * Validate that the image file meeting the dimension constraints.
     *
     * @param DimensionsRule $rule a documentable dimensions rule object.
     */
    public function dimensions(DimensionsRule $rule): self
    {
        return $this->addRule($rule);
    }

    /**
     * Validate that the file has one of the given mimetypes.
     *
     * @var string[] $mimetypes e.g. image/jpeg, text/csv
     */
    public function mimetypes(array $mimetypes): self
    {
        return $this->addSimpleRule(
            'mimetypes', $mimetypes,
            $this->trans('mimetypes', ['values' => implode(' or ', $mimetypes)])
        );
    }

    /**
     * Validate that the file has one of the given mime types.
     *
     * @var string[] $mimes e.g. jpeg, png
     */
    public function mimes(array $mimes): self
    {
        return $this->addSimpleRule(
            'mimes', $mimes,
            $this->trans('mimes', ['values' => implode(' or ', $mimes)])
        );
    }

    public function getType(): string
    {
        return 'file';
    }
}
