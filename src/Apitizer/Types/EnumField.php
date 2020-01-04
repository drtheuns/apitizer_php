<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;
use UnexpectedValueException;

/**
 * Specialization of the field type to display enumerable values.
 *
 * This can be especially helpful when you have, for example, some kind of
 * "status" field and you want to document all the available states.
 */
class EnumField extends Field
{
    /**
     * @var array
     */
    protected $enum = [];

    public function __construct(
        QueryBuilder $queryBuilder,
        string $key,
        array $enum,
        string $type = 'string'
    ) {
        parent::__construct($queryBuilder, $key, $type);
        $this->enum = $enum;
    }

    protected function validateValue($value)
    {
        $value = parent::validateValue($value);

        if (! in_array($value, $this->enum)) {
            throw new UnexpectedValueException("Expected value [{$value}] to be one of [{implode(',', $this->enum)}]");
        }

        return $value;
    }

    public function printType()
    {
        return $this->typeOrNull("enum of {$this->type}");
    }

    public function getEnum()
    {
        return $this->enum;
    }
}