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
     * @var array an associative array where the key is the name of the filter,
     * and the value is just the value of that filter.
     */
    public $filters = [];
}
