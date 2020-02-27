<?php

namespace Apitizer\Validation;

use Apitizer\Validation\ObjectRules;
use Illuminate\Contracts\Validation\Rule;

class RuleInterpreter
{
    public static function rulesFrom(TypedRuleBuilder $builder)
    {
        $rules = [];
        static::makeRules($rules, $builder);

        return $rules;
    }

    protected static function makeRules(array &$rules, TypedRuleBuilder $builder) {
        $path = $builder->getValidationRuleName();

        if (! empty($path)) {
            // Render the rules for this builder.
            $rules[$path] = static::toValidationRules($builder);
        }

        if ($builder instanceof ContainerType) {
            foreach ($builder->getChildren() as $childBuilder) {
                static::makeRules($rules, $childBuilder);
            }
        }
    }

    protected static function toValidationRules(TypedRuleBuilder $builder)
    {
        $rules = [];

        if ($builder->isRequired()) {
            $rules[] = 'required';
        }

        if ($type = $builder->getValidatableType()) {
            $rules[] = $type;
        }

        if ($builder->isNullable()) {
            $rules[] = 'nullable';
        }

        foreach ($builder->getRules() as $rule) {
            if ($rule instanceof ValidationRule) {
                $rules[] = $rule->toValidationRule();
            }

            if (is_string($rule) || $rule instanceof Rule) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }
}
