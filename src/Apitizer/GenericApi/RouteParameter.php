<?php

namespace Apitizer\GenericApi;

use Apitizer\Schema;
use Illuminate\Database\Eloquent\Model;

class RouteParameter
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var string
     */
    protected $parameterName;

    /**
     * @var string|null
     */
    protected $associationName;

    /**
     * @var string|null the value that the route parameter has.
     */
    protected $value;

    public function __construct(string $parameterName, Schema $schema, ?string $associationName)
    {
        $this->parameterName = $parameterName;
        $this->schema = $schema;
        $this->associationName = $associationName;
    }

    /**
     * @param string $parameterName
     * @param array{schema: class-string,
     *              has_param: bool,
     *              association: string|null} $metadata
     */
    public static function fromRouteMetadata(string $parameterName, array $metadata): self
    {
        $schema = $metadata['schema'];
        $schema = new $schema;

        return new static($parameterName, $schema, $metadata['association']);
    }

    public function getSchema(): Schema
    {
        return $this->schema;
    }

    public function getAssociationName(): ?string
    {
        return $this->associationName;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
