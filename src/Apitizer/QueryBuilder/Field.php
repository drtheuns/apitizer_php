<?php

namespace Apitizer\QueryBuilder;

use Apitizer\FieldType;

class Field
{
    /**
     * The name of the field that the client uses.
     *
     * @var string
     */
    protected $name;

    /**
     * The internal type that is used for this field.
     *
     * @var FieldType
     */
    protected $type;

    public function __construct(string $name, FieldType $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function render($row)
    {
        return $this->type->render($row[$this->type->getKey()]);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }
}
