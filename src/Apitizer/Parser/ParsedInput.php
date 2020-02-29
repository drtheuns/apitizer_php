<?php

namespace Apitizer\Parser;

class ParsedInput
{
    /**
     * @var string[]
     */
    public $fields = [];

    /**
     * @var Relation[]
     */
    public $associations = [];

    /**
     * @var Sort[]
     */
    public $sorts = [];

    /**
     * @var array<string, mixed>
     */
    public $filters = [];

    public function addField(string $field): void
    {
        $this->fields[] = $field;
    }

    public function addRelation(Relation $relation): void
    {
        $this->associations[] = $relation;
    }
}
