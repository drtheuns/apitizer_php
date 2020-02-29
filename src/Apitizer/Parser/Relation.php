<?php

namespace Apitizer\Parser;

class Relation
{
    /** @var string */
    public $name;

    /** @var string[] */
    public $fields;

    /** @var Relation[] */
    public $associations = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addField(string $field): void
    {
        $this->fields[] = $field;
    }

    public function addRelation(Relation $relation): void
    {
        $this->associations[] = $relation;
    }
}
