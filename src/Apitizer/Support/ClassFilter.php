<?php

namespace Apitizer\Support;

use FilterIterator;
use ReflectionClass;
use Iterator;

/**
 * An iterator that filters php files that follow PSR-4 to only those that
 * extend some class and returns the fully-qualified namespace for those
 * classes.
 */
class ClassFilter extends FilterIterator
{
    /**
     * @var string the base namespace to use for all classes.
     */
    protected $namespace;

    /**
     * @var string the class that must be extended from.
     */
    protected $class;

    /**
     * @var string the namespace to the current file we're handling. This will
     * be the return value of it passes the accept function.
     */
    protected $current;

    public function __construct(string $namespace, string $class, Iterator $iterator)
    {
        parent::__construct($iterator);
        $this->namespace = $namespace;
        $this->class = $class;
    }

    public function current()
    {
        return $this->current;
    }

    public function accept()
    {
        $fileInfo = $this->getInnerIterator()->current();

        // TODO: What if we're using a recursive iterator?
        $this->current = $this->namespace . '\\' . $fileInfo->getBasename('.php');

        try {
            $reflection = new ReflectionClass($this->current);

            if (! $reflection->isInstantiable()) {
                return false;
            }

            return $reflection->isSubclassOf($this->class);
        } catch (\Exception $e) {
            return false;
        }
    }
}
