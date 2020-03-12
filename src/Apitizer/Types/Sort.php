<?php

namespace Apitizer\Types;

use Apitizer\Schema;
use Apitizer\Sorting\ColumnSort;

class Sort extends Factory
{
    /**
     * @var string 'asc' | 'desc'
     */
    protected $order = 'asc';

    /**
     * @var callable|null
     */
    protected $handler;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    public function byField(string $field): self
    {
        $this->handleUsing(new ColumnSort($field));

        return $this;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function setOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getHandler(): ?callable
    {
        return $this->handler;
    }

    public function handleUsing(callable $handler): self
    {
        $this->handler = $handler;

        return $this;
    }
}
