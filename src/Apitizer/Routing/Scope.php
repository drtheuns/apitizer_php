<?php

namespace Apitizer\Routing;

use Apitizer\Schema;
use Closure;

class Scope
{
    /**
     * @var array{readable?: ScopeAffordance, creatable?: ScopeAffordance,
     *            updateable?: ScopeAffordance, deleteable?: ScopeAffordance}
     *
     * The various affordances of this scope.
     */
    protected $affordances = [];

    /** @var array<string, Scope> */
    protected $associations = [];

    /**
     * @var string the name of the URL segment for this builder.
     */
    protected $pathName;

    /**
     * @var Scope|null
     */
    protected $parent;

    /**
     * @var string|null the name of the association through which the parent got
     * to this scope.
     */
    protected $name;

    public function __construct(Scope $parent = null, string $name = null)
    {
        $this->parent = $parent;
        $this->name = $name;
    }

    public function readable(): self
    {
        $this->affordances['readable'] = new ScopeAffordance('readable');

        return $this;
    }

    /**
     * @param class-string $serviceClass
     * @param string $method
     */
    public function creatable(string $serviceClass = null, string $method = null): self
    {
        $this->affordances['creatable'] = new ScopeAffordance('creatable', $serviceClass, $method);

        return $this;
    }

    /**
     * @param class-string $serviceClass
     * @param string $method
     */
    public function updatable(string $serviceClass = null, string $method = null): self
    {
        $this->affordances['updatable'] = new ScopeAffordance('updatable', $serviceClass, $method);

        return $this;
    }

    /**
     * @param class-string $serviceClass
     * @param string $method
     */
    public function deletable(string $serviceClass = null, string $method = null): self
    {
        $this->affordances['deletable'] = new ScopeAffordance('deletable', $serviceClass, $method);

        return $this;
    }

    /**
     * Define all CRUD actions for this resource.
     *
     * @param class-string|null $serviceClass
     */
    public function crud(string $serviceClass = null): self
    {
        $this->readable()
             ->creatable($serviceClass)
             ->updatable($serviceClass)
             ->deletable($serviceClass);

        return $this;
    }

    /**
     * Define the scopes for a relationship.
     *
     * @param string $name the name of the association as it's defined in the
     * associations callback.
     * @param Closure(Scope $scope): mixed $closure
     */
    public function association(string $name, Closure $closure): self
    {
        $scope = new Scope($this, $name);

        $closure($scope);

        $this->associations[$name] = $scope;

        return $this;
    }

    /**
     * Defines an association with all the typical crud operations.
     *
     * @param string $name
     * @param class-string $serviceClass
     */
    public function associationCrud(string $name, string $serviceClass = null): self
    {
        return $this->association($name, function (Scope $scope) use ($serviceClass) {
            $scope->crud($serviceClass);
        });
    }

    /**
     * Set the path segment name for this builder.
     *
     * By default, the name of the builder will be wrangled to a path segment.
     * For example, PostSchema would turn into /posts.
     */
    public function path(string $name): self
    {
        $this->pathName = $name;

        return $this;
    }

    /**
     * @internal
     * @return array<string, ScopeAffordance>
     */
    public function getAffordances(): array
    {
        return $this->affordances;
    }

    /**
     * @internal
     * @return array<string, Scope>
     */
    public function getAssociations(): array
    {
        return $this->associations;
    }

    /**
     * @internal
     */
    public function getPath(): ?string
    {
        return $this->pathName;
    }

    /**
     * @internal
     */
    public function getParent(): ?Scope
    {
        return $this->parent;
    }

    /**
     * @internal
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
