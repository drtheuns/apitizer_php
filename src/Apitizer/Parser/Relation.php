<?php

namespace Apitizer\Parser;

class Relation
{
    /** @var string */
    public $name;

    /** @var array<string|Relation> */
    public $fields;

    /**
     * @param string $name
     * @param array<string|Relation> $fields
     */
    public function __construct(string $name, array $fields) {
        $this->name = $name;
        $this->fields = $fields;
    }
}
