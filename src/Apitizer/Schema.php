<?php

namespace Apitizer;

use Apitizer\Exceptions\ClassFinderException;
use Apitizer\Exceptions\SchemaDefinitionException;
use Apitizer\Support\ComposerNamespaceClassFinder;

/**
 * The schema class is responsible for registering all the query builders.
 *
 * The registered query builders are used to:
 *
 *   - Generate documentation
 *   - Validate the schema with apitizer:validate-schema
 */
abstract class Schema
{
    /**
     * @var QueryBuilder[] the list of register query builders.
     */
    protected $registeredBuilders = [];

    public function __construct()
    {
        $this->registerBuilders();
        $this->registeredBuilders = array_unique($this->registeredBuilders);
    }

    /**
     * Registration of all query builders should happen inside this function.
     */
    abstract protected function registerBuilders();

    /**
     * Register one or many new query builders.
     */
    protected function register($builderClass): self
    {
        $classes = is_array($builderClass) ? $builderClass : func_get_args();

        foreach ($classes as $class) {
            if (! (is_string($class) && class_exists($class) && (new $class) instanceof QueryBuilder)) {
                throw SchemaDefinitionException::notAQueryBuilder($class);
            }
        }

        $this->registeredBuilders = array_merge($this->registeredBuilders, $classes);

        return $this;
    }

    /**
     * Register all query builder classes that start with the given namespace.
     *
     * NOTE: This requires composer to be used and the project to be following
     * PSR-4 conventions.
     */
    protected function registerFromNamespace(string $namespace, string $projectRoot = null): self
    {
        try {
            $classes = ComposerNamespaceClassFinder::make($namespace, QueryBuilder::class)
                ->startingFrom($projectRoot)
                ->all();
        } catch (ClassFinderException $e) {
            throw SchemaDefinitionException::namespaceLookupFailed($namespace, $e);
        }

        $this->registeredBuilders = array_merge($this->registeredBuilders, $classes);

        return $this;
    }

    /**
     * @return string[]
     */
    public function getQueryBuilders(): array
    {
        return $this->registeredBuilders;
    }
}
