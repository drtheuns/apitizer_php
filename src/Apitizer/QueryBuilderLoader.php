<?php

namespace Apitizer;

use Apitizer\Exceptions\ClassFinderException;
use Apitizer\QueryBuilder;
use Apitizer\Support\ComposerNamespaceClassFinder;

class QueryBuilderLoader
{
    /**
     * @var string[]
     */
    protected $queryBuilders;

    public function loadFromConfig()
    {
        $this->queryBuilders = array_unique(array_merge(
            $this->loadIndividualClasses(),
            $this->loadNamespaces()
        ));
    }

    public function loadIndividualClasses()
    {
        return config('apitizer.query_builders.classes');
    }

    public function loadNamespaces()
    {
        $classes = [];

        foreach (config('apitizer.query_builders.namespaces', []) as $namespace) {
            $classes = array_merge($classes, $this->loadFromNamespace($namespace));
        }

        return $classes;
    }

    public function loadFromNamespace(string $namespace)
    {
        return ComposerNamespaceClassFinder::make($namespace, QueryBuilder::class)->all();
    }

    public function getQueryBuilders()
    {
        if (is_null($this->queryBuilders)) {
            $this->loadFromConfig();
        }

        return $this->queryBuilders;
    }
}
