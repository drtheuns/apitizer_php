<?php

namespace Apitizer\Validation;

class ObjectRuleBuilder
{
    /**
     * @var array<string, $type>
     */
    protected $fields = [];

    /**
     * @var string|null
     */
    protected $fieldName;

    public function __construct(string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function string(string $name)
    {
        return $this->field(new StringRuleBuilder($name));
    }

    public function object(string $name)
    {
        return $this->field(new ObjectRuleBuilder($name));
    }

    public function array(string $name)
    {
        return $this->field(new ArrayFieldBuilder($name));
    }

    // TODO: Add interface for introspectable type.
    public function field(string $name, $type)
    {
        // TODO Add exception when redefining the same field.
        $this->fields[$type->getFieldName()] = $type;

        return $this;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }
}
