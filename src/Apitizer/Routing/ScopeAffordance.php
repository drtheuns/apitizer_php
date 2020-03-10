<?php

namespace Apitizer\Routing;

/**
 * Holds details about an affordance with a scope.
 *
 * The word affordance is taken from interaction design to mean the possible
 * actions that some object "affords" / is capable of.
 */
class ScopeAffordance
{
    /** @var string */
    protected $name;

    /** @var class-string|null */
    protected $service;

    /** @var string|null */
    protected $method;

    /**
     * @param string $name
     * @param class-string|null $service
     * @param string|null $method
     */
    public function __construct(string $name, ?string $service = null, ?string $method = null)
    {
        $this->name = $name;
        $this->service = $service;
        $this->method = $method;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }
}
