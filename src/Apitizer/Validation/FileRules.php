<?php

namespace Apitizer\Validation;

use Apitizer\Validation\Rules\DimensionsRule;
use Apitizer\Validation\Rules\MimesRule;
use Apitizer\Validation\Rules\MimetypesRule;

class FileRules extends FieldRuleBuilder
{
    use Concerns\SharedRules;

    public function image(): self
    {
        return $this->addConstraint('image');
    }

    public function dimensions(DimensionsRule $rule): self
    {
        return $this->addRule($rule);
    }

    /**
     * @param string[] $mimetypes
     */
    public function mimetypes(array $mimetypes): self
    {
        return $this->addRule(new MimetypesRule($mimetypes));
    }

    /**
     * @pararm string[] $mimes
     */
    public function mimes(array $mimes): self
    {
        return $this->addRule(new MimesRule($mimes));
    }

    public function getType(): string
    {
        return 'file';
    }
}
