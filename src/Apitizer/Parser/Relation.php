<?php

namespace Apitizer\Parser;

class Relation
{
    /** @var string */
    public $name;

    /** @var array */
    public $fields;

    public function __construct(string $name, array $fields) {
        $this->name = $name;
        $this->fields = $fields;
    }
}
