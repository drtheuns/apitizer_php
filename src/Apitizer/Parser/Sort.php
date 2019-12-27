<?php

namespace Apitizer\Parser;

class Sort
{
    // ASC|DESC NULLS LAST|FIRST not supported because laravel does not support
    // these (they're DB-dependant)
    const ASC = 'asc';
    const DESC = 'desc';

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $order;

    public function __construct(string $field, string $order)
    {
        $this->field = $field;
        $this->order = $order;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getOrder()
    {
        return $this->order;
    }
}
