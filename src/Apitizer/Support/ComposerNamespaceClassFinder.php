<?php

namespace Apitizer\Support;

use Apitizer\Exceptions\ClassFinderException;
use ArrayIterator;
use DirectoryIterator;
use IteratorAggregate;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Iterator;
use RecursiveDirectoryIterator;
use RegexIterator;

/**
 * A helper class to search for all classes that extends some other class within
 * a namespace. This is used to find all classes that extend the query builder.
 */
class ComposerNamespaceClassFinder implements IteratorAggregate
{
    /**
     * @var string the base namespace that will be searched.
     */
    protected $namespace;

    /**
     * @var string the class that must be inherited from.
     */
    protected $instanceofClass;

    /**
     * @var null|string the project directory that should contain the composer.json file.
     */
    protected $projectRoot;

    /**
     * @var bool whether or not the search the namespace recursively.
     */
    protected $recursive = false;

    public function __construct(string $namespace, string $class)
    {
        $this->namespace = ltrim($namespace, '\\');
        $this->instanceofClass = $class;
    }

    /**
     * @return ComposerNamespaceClassFinder<string>
     */
    public static function make(string $namespace, string $class)
    {
        return new static($namespace, $class);
    }

    /**
     * Set the project root where the composer.json should be.
     *
     * @return ComposerNamespaceClassFinder<string>
     */
    public function startingFrom(?string $projectRoot): self
    {
        $this->projectRoot = $projectRoot;

        return $this;
    }

    /**
     * Whether or not the search process should be done recursively over all
     * subdirectories.
     *
     * @return ComposerNamespaceClassFinder<string>
     */
    public function recursively(bool $recursively): self
    {
        $this->recursive = $recursively;

        return $this;
    }

    /**
     * Get all the classes that satisfy the constraints.
     *
     * @throws ClassFinderException
     *
     * @return array<string>
     */
    public function all(): array
    {
        return iterator_to_array($this->getIterator());
    }

    /**
     * @throws ClassFinderException
     *
     * @return Iterator<string>
     */
    public function getIterator(): Iterator
    {
        $projectRoot = $this->projectRoot ?? $this->findProjectRoot();
        $composerFile = "$projectRoot/composer.json";

        if (! file_exists($composerFile)) {
            throw ClassFinderException::composerFileNotFound($composerFile);
        }

        if (! $content = file_get_contents($composerFile)) {
            throw ClassFinderException::composerFileNotFound($composerFile);
        }

        $composerContent = json_decode($content, true);
        if (! $psr4 = Arr::get($composerContent, 'autoload.psr-4')) {
            throw ClassFinderException::psr4NotFound($composerFile);
        }

        // Find the first registered psr-4 namespace that starts with
        // the namespace that we're looking for.
        foreach ($psr4 as $namespace => $path) {
            if (Str::startsWith($this->namespace, $namespace)) {
                // Remove the base portion of the namespace from the namespace
                // we're looking for, and set the path that we know of so far to
                // what is specified in the composer.json
                $remainderNamespace = substr($this->namespace, strlen($namespace));
                $path = $projectRoot . DIRECTORY_SEPARATOR . $path;

                // Construct the remainder of the path based on the namespace.
                // This can only be done by assuming PSR-4.
                $pathForNamespace = str_replace('\\', DIRECTORY_SEPARATOR, $remainderNamespace);
                $path = Str::endsWith($path, DIRECTORY_SEPARATOR)
                      ? $path . $pathForNamespace
                      : $path . DIRECTORY_SEPARATOR . $pathForNamespace;

                return $this->iteratorForPath($path);
            }
        }

        return new ArrayIterator([]);
    }

    /**
     * @return Iterator<string>
     */
    private function iteratorForPath(string $path): Iterator
    {
        $directoryIterator = $this->recursive
                           ? new RecursiveDirectoryIterator($path)
                           : new DirectoryIterator($path);

        // We're only interested in PHP files.
        $iterator = new RegexIterator($directoryIterator, '/\.php$/');

        // Filter the files that are found to those that extend the given class,
        // and return the fully qualified namespace for those classes.
        return new ClassFilter($this->namespace, $this->instanceofClass, $iterator);
    }

    /**
     * Do a best attempt at finding the project root.
     */
    private function findProjectRoot(): ?string
    {
        // Laravel provides a base_path method we can use.
        if (function_exists('base_path')) {
            return base_path();
        }

        // Start at this project's parent directory.
        $directory = realpath(__DIR__ . str_repeat(DIRECTORY_SEPARATOR . '..', 4));

        if (! $directory) {
            return null;
        }

        // Check if this project is contained within a vendor directory.
        if (basename($directory) === 'vendor') {
            // If so, return the directory that contains the vendor dir, which
            // should be the project root.
            return $directory . DIRECTORY_SEPARATOR . '..';
        }

        // Otherwise, give up.
        // We could attempt to traverse the entire filesystem upwards in an
        // attempt to find a composer.json, but at that point it would probably
        // be better to force the developers to specify the actual project root.
        return null;
    }
}
