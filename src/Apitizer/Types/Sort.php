<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;
use Apitizer\Sorting\ColumnSort;

class Sort extends Factory
{
    /**
     * @var string 'asc' | 'desc'
     */
    protected $order = 'asc';

    /**
     * @var callable
     */
    protected $handler;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function byField(string $field): self
    {
        $this->handleUsing(new ColumnSort($field));

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function handleUsing(callable $handler): self
    {
        $this->handler = $handler;

        return $this;
    }
}
