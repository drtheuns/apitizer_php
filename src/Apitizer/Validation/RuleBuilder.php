<?php

namespace Apitizer\Validation;

use Illuminate\Support\Collection;

class RuleBuilder
{
    /**
     * @var TypedRuleBuilder[]
     */
    protected $fields = [];

    public function string(string $name): StringRuleBuilder
    {
        return $this->field(new StringRuleBuilder($name));
    }

    public function uuid(string $name): StringRuleBuilder
    {
        return $this->string($name)->uuid();
    }

    public function boolean(string $name): BooleanRuleBuilder
    {
        return $this->field(new BooleanRuleBuilder($name));
    }

    public function date(string $name): DateRuleBuilder
    {
        return $this->field(new DateRuleBuilder($name));
    }

    public function datetime(string $name): DateTimeRuleBuilder
    {
        return $this->field(new DateTimeRuleBuilder($name));
    }

    public function array(string $name): ArrayRuleBuilder
    {
        return $this->field(new ArrayRuleBuilder($name));
    }

    public function number(string $name): NumberRuleBuilder
    {
        return $this->field(new NumberRuleBuilder($name));
    }

    public function integer(string $name): NumberRuleBuilder
    {
        return $this->field(new IntegerRuleBuilder($name));
    }

    public function file(string $name): FileRuleBuilder
    {
        return $this->field(new FileRuleBuilder($name));
    }

    public function image(string $name): FileRuleBuilder
    {
        return $this->file($name)->image();
    }

    /**
     * Add a new field to the list of rules.
     */
    public function field(TypedRuleBuilder $builder): TypedRuleBuilder
    {
        $this->fields[$builder->getName()] = $builder;

        return $builder;
    }

    /**
     * @internal
     *
     * @return array<string, TypedRuleBuilder>
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * @internal
     */
    public function toValidationRules()
    {
        $rules = new Collection();

        foreach ($this->fields as $field) {
            $field->addValidationRulesTo($rules);
        }

        return $rules->all();
    }

    public function getDocumentedFields(): array
    {
        $fields = new Collection();

        foreach ($this->fields as $field) {
            $field->addDocumentationTo($fields);
        }

        return $fields->all();
    }
}
