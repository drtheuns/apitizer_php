<?php

namespace Apitizer\Types;

use Apitizer\Schema;

/**
 * Boiler plate that is common across sorting, filters, fields, etc
 */
abstract class Factory
{
    /**
     * @var Schema
     */
    protected $schema;

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

    public function __construct(Schema $schema)
    {
        $this->setSchema($schema);
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

    public function getSchema(): Schema
    {
        return $this->schema;
    }

    public function setSchema(Schema $schema): self
    {
        $this->schema = $schema;

        return $this;
    }
}
