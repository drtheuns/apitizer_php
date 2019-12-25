<?php

namespace Apitizer\Types;

class Sort
{
    // ASC|DESC NULLS LAST|FIRST not supported because laravel does not support
    // these (they're DB-dependant)
    const ASC = 'asc';
    const DESC = 'desc';

    protected $field;
    protected $order;
    protected $handler;

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

    public function getHandler()
    {
        return $this->handler;
    }

    public function setHandler(callable $handler)
    {
        $this->handler = $handler;
    }
}
