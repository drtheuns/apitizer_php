<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;

/**
 * Boiler plate that is common across sorting, filters, fields, etc
 */
abstract class Factory
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * The name that is available to the client
     *
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Set the description that will be available in the API documentation.
     *
     * @var string
     *
     * @return Factory
     */
    public function description(string $description): Factory
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(QueryBuilder $queryBuilder): self
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }
}
