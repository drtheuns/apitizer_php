<?php

namespace Apitizer\Types;

use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\Schema;

/**
 * Specialization of the field type to display enumerable values.
 *
 * This can be especially helpful when you have, for example, some kind of
 * "status" field and you want to document all the available states.
 */
class EnumField extends Field
{
    /**
     * @var array<mixed>
     */
    protected $enum = [];

    /**
     * @param Schema $schema
     * @param string $key
     * @param array<mixed> $enum
     * @param string $type
     */
    public function __construct(
        Schema $schema,
        string $key,
        array $enum,
        string $type = 'string'
    ) {
        parent::__construct($schema, $key, $type);
        $this->enum = $enum;
    }

    public function validateValue($value, $row)
    {
        $value = parent::validateValue($value, $row);

        if (! in_array($value, $this->enum)) {
            throw InvalidOutputException::invalidEnum($this, $value, $row);
        }

        return $value;
    }

    public function printType(): string
    {
        return $this->typeOrNull("enum of {$this->type}");
    }

    /**
     * @return array<mixed>
     */
    public function getEnum(): array
    {
        return $this->enum;
    }
}
