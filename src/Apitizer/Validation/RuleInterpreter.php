<?php

namespace Apitizer\Validation;

use Illuminate\Contracts\Validation\Rule;

class RuleInterpreter
{
    /**
     * @param TypedRuleBuilder $builder
     *
     * @return array<string, string|Rule>
     */
    public static function rulesFrom(TypedRuleBuilder $builder): array
    {
        $rules = [];
        static::makeRules($rules, $builder);

        return $rules;
    }

    /**
     * @param array<string, string|Rule> $rules
     * @param TypedRuleBuilder $builder
     *
     * @return void
     */
    protected static function makeRules(array &$rules, TypedRuleBuilder $builder): void
    {
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

    /**
     * @param TypedRuleBuilder $builder
     *
     * @return (string|Rule)[]
     */
    protected static function toValidationRules(TypedRuleBuilder $builder): array
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
