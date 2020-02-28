<?php

namespace Apitizer\Validation;

use Illuminate\Contracts\Validation\Rule;
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
        $stringRules = new StringRules($name);

        $this->addField($stringRules);

        return $stringRules;
    }

    public function uuid(string $name): StringRules
    {
        return $this->string($name)->uuid();
    }

    public function boolean(string $name): BooleanRules
    {
        $booleanRules = new BooleanRules($name);

        $this->addField($booleanRules);

        return $booleanRules;
    }

    public function date(string $name, string $format = null): DateRules
    {
        $dateRules = DateRules::date($name, $format);

        $this->addField($dateRules);

        return $dateRules;
    }

    public function datetime(string $name, string $format = null): DateRules
    {
        $dateRules = DateRules::datetime($name, $format);

        $this->addField($dateRules);

        return $dateRules;
    }

    public function number(string $name): NumberRules
    {
        $numberRules = new NumberRules($name);

        $this->addField($numberRules);

        return $numberRules;
    }

    public function integer(string $name): IntegerRules
    {
        $integerRules = new IntegerRules($name);

        $this->addField($integerRules);

        return $integerRules;
    }

    public function file(string $name): FileRules
    {
        $fileRules = new FileRules($name);

        $this->addField($fileRules);

        return $fileRules;
    }

    public function image(string $name): FileRules
    {
        return $this->file($name)->image();
    }

    public function object(string $name, Closure $callback): ObjectRules
    {
        $objectRules = new ObjectRules($name, $callback);

        $this->addField($objectRules);

        return $objectRules;
    }

    public function array(string $name): ArrayRules
    {
        $arrayRules = new ArrayRules($name);

        $this->addField($arrayRules);

        return $arrayRules;
    }

    private function addField(TypedRuleBuilder $type): void
    {
        $type->setPrefix($this->getRulePrefix());

        // TODO Add exception when redefining the same field.
        $this->fields[$type->getFieldName()] = $type;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function getType(): string
    {
        return 'object';
    }

    /**
     * @return null
     */
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

    public function resolve(): void
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
