<?php

namespace Apitizer\Validation;

use Closure;

class ObjectRules extends FieldRuleBuilder implements ContainerType
{
    use Concerns\SharedRules;

    /**
     * @var array<string, TypedRuleBuilder>
     */
    protected $fields = [];

    /**
     * @var string|null
     */
    protected $fieldName;

    /**
     * @var Closure
     */
    protected $callback;

    public function __construct(?string $fieldName, Closure $callback)
    {
        parent::__construct($fieldName);
        $this->callback = $callback;
    }

    public function string(string $name): StringRules
    {
        return $this->field(new StringRules($name));
    }

    public function uuid(string $name): StringRules
    {
        return $this->string($name)->uuid();
    }

    public function boolean(string $name): BooleanRules
    {
        return $this->field(new BooleanRules($name));
    }

    public function date(string $name, string $format = null): DateRules
    {
        return $this->field(DateRules::date($name, $format));
    }

    public function datetime(string $name, string $format = null): DateRules
    {
        return $this->field(DateRules::datetime($name, $format));
    }

    public function number(string $name): NumberRules
    {
        return $this->field(new NumberRules($name));
    }

    public function integer(string $name): IntegerRules
    {
        return $this->field(new IntegerRules($name));
    }

    public function file(string $name): FileRules
    {
        return $this->field(new FileRules($name));
    }

    public function image(string $name): FileRules
    {
        return $this->file($name)->image();
    }

    public function object(string $name, Closure $callback): ObjectRules
    {
        return $this->field(new ObjectRules($name, $callback));
    }

    public function array(string $name): ArrayRules
    {
        return $this->field(new ArrayRules($name));
    }

    public function field(TypedRuleBuilder $type): TypedRuleBuilder
    {
        $type->setPrefix($this->getRulePrefix());

        // TODO Add exception when redefining the same field.
        $this->fields[$type->getFieldName()] = $type;

        return $type;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function getType(): string
    {
        return 'object';
    }

    public function getValidatableType()
    {
        return null;
    }

    /**
     * @return TypedRuleBuilder[]
     */
    public function getChildren(): array
    {
        return $this->fields;
    }

    public function getRulePrefix(): string
    {
        if (empty($this->getValidationRuleName())) {
            return '';
        }

        return $this->getValidationRuleName() . '.';
    }

    public function resolve()
    {
        ($this->callback)($this);

        // Also resolve all the nested objects.
        foreach ($this->getChildren() as $field) {
            if ($field instanceof ContainerType) {
                $field->resolve();
            }
        }
    }
}
