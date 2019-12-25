<?php

namespace Apitizer\Parser;

class Sort
{
    // ASC|DESC NULLS LAST|FIRST not supported because laravel does not support
    // these (they're DB-dependant)
    const ASC = 'asc';
    const DESC = 'desc';

    public $field;
    public $order;

    public function __construct(string $field, string $order)
    {
        $this->field = $field;
        $this->order = $order;
    }
}
