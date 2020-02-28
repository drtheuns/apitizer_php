<?php

namespace Apitizer;

use Apitizer\QueryBuilder;
use Apitizer\Support\ComposerNamespaceClassFinder;

/**
 * The loader responsible for preparing a list of query builders based on the
 * configuration. Query builders can be references directly by name, or by
 * using the namespace (assuming PSR-4 and composer are used).
 */
class QueryBuilderLoader
{
    /**
     * @var string[]|null
     */
    protected $queryBuilders;

    /**
     * Load all query builders based on the config/apitizer.php configuration.
     */
    public function loadFromConfig(): void
    {
        $this->queryBuilders = array_unique(array_merge(
            $this->loadIndividualClasses(),
            $this->loadNamespaces()
        ));
    }

    /**
     * Load all the classes that were registered by name directly.
     *
     * @return string[]
     */
    protected function loadIndividualClasses(): array
    {
        return config('apitizer.query_builders.classes');
    }

    /**
     * Load all query builders from the registered namespaces.
     *
     * @return string[]
     */
    protected function loadNamespaces(): array
    {
        $classes = [];

        foreach (config('apitizer.query_builders.namespaces', []) as $namespace) {
            $classes = array_merge($classes, $this->loadFromNamespace($namespace));
        }

        return $classes;
    }

    /**
     * Load the query builders non recursively from a namespace.
     *
     * @param string $namespace
     * @return string[]
     */
    protected function loadFromNamespace(string $namespace): array
    {
        return ComposerNamespaceClassFinder::make($namespace, QueryBuilder::class)->all();
    }

    /**
     * Get a list of fully-qualified namespaces to the registered query builders.
     *
     * @return string[]
     */
    public function getQueryBuilders(): array
    {
        if (is_null($this->queryBuilders)) {
            $this->loadFromConfig();
        }

        return $this->queryBuilders ?? [];
    }
}
