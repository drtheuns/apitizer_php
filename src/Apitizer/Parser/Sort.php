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

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string 'asc' | 'desc'
     */
    public function getOrder(): string
    {
        return $this->order;
    }
}
