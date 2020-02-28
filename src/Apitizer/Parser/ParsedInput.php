<?php

namespace Apitizer\Parser;

class ParsedInput
{
    /**
     * @var (string|Relation)[] an array of either strings (plain columns) or
     * Relation objects which denote associations.
     */
    public $fields = [];

    /**
     * @var Sort[]
     */
    public $sorts = [];

    /**
     * @var array<string, mixed>
     */
    public $filters = [];
}
