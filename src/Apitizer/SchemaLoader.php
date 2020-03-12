<?php

namespace Apitizer;

use Apitizer\Schema;
use Apitizer\Support\ComposerNamespaceClassFinder;

/**
 * The loader responsible for preparing a list of schemas based on the
 * configuration. schemas can be references directly by name, or by
 * using the namespace (assuming PSR-4 and composer are used).
 */
class SchemaLoader
{
    /**
     * @var string[]|null
     */
    protected $schemas;

    /**
     * Load all schemas based on the config/apitizer.php configuration.
     */
    public function loadFromConfig(): void
    {
        $this->schemas = array_unique(array_merge(
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
        return config('apitizer.schemas.classes');
    }

    /**
     * Load all schemas from the registered namespaces.
     *
     * @return string[]
     */
    protected function loadNamespaces(): array
    {
        $classes = [];

        foreach (config('apitizer.schemas.namespaces', []) as $namespace) {
            $classes = array_merge($classes, $this->loadFromNamespace($namespace));
        }

        return $classes;
    }

    /**
     * Load the schemas non recursively from a namespace.
     *
     * @param string $namespace
     * @return string[]
     */
    protected function loadFromNamespace(string $namespace): array
    {
        return ComposerNamespaceClassFinder::make($namespace, Schema::class)->all();
    }

    /**
     * Get a list of fully-qualified namespaces to the registered schemas.
     *
     * @return string[]
     */
    public function getSchemas(): array
    {
        if (is_null($this->schemas)) {
            $this->loadFromConfig();
        }

        return $this->schemas ?? [];
    }
}
