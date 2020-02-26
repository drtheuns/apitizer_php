<?php

namespace Apitizer\Validation;

use Illuminate\Support\Collection;

class ArrayRuleBuilder extends TypedRuleBuilder
{
    /**
     * @var ArrayValueRuleBuilder the builder for the type internal to the array.
     */
    protected $valueRuleBuilder;

    public function distinct(): self
    {
        return $this->addSimpleRule('distinct');
    }

    public function whereEach(): ArrayValueRuleBuilder
    {
        $builder = new ArrayValueRuleBuilder($this->getName() . '.*');

        $this->valueRuleBuilder = $builder;

        return $builder;
    }

    /**
     * @internal
     */
    public function addValidationRulesTo(Collection $rules): Collection
    {
        $rules = parent::addValidationRulesTo($rules);

        return $this->valueRuleBuilder
            ? $this->valueRuleBuilder->addValidationRulesTo($rules)
            : $rules;
    }

    /**
     * @internal
     */
    public function addDocumentedFieldsTo(Collection $rules): Collection
    {
        return $this->valueRuleBuilder
            ? $this->valueRuleBuilder->addDocumentationTo($rules)
            : $rules;
    }

    public function getType(): string
    {
        return 'array';
    }
}
