<?php

namespace Apitizer\Types;

use Apitizer\Types\AbstractField;
use Apitizer\Types\Association;
use Apitizer\Types\Sort;
use Apitizer\Types\Filter;
use Apitizer\Types\Factory;

/**
 * The fetch specification holds all the validated information that was
 * requested by the client.
 */
class FetchSpec
{
    /**
     * The fields that should be fetched.
     *
     * @var AbstractField[]
     */
    protected $fields = [];

    /**
     * The associations that should be fetched.
     *
     * @var Association[]
     */
    protected $associations;

    /**
     * The sorting methods that should be applied.
     *
     * @var Sort[]
     */
    protected $sorts = [];

    /**
     * The filters that should be applied to the datasource.
     *
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * @param AbstractField[] $fields
     * @param Association[] $associations
     * @param Sort[] $sorts
     * @param Filter[] $filters
     */
    public function __construct(
        array $fields = [],
        array $associations = [],
        array $sorts = [],
        array $filters = []
    ) {
        $this->fields = $fields;
        $this->associations = $associations;
        $this->sorts = $sorts;
        $this->filters = $filters;
    }

    /**
     * @return AbstractField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return Sort[]
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return Association[]
     */
    public function getAssociations(): array
    {
        return $this->associations;
    }

    public function fieldSelected(string $name): bool
    {
        return $this->hasName($this->getFields(), $name);
    }

    public function associationSelected(string $name): bool
    {
        return $this->hasName($this->getAssociations(), $name);
    }

    public function filterSelected(string $name): bool
    {
        return $this->hasName($this->getFilters(), $name);
    }

    public function sortSelected(string $name): bool
    {
        return $this->hasName($this->getSorts(), $name);
    }

    /**
     * @param Factory[] $array
     * @param string $name
     * @return bool
     */
    private function hasName(array $array, string $name): bool
    {
        foreach ($array as $factory) {
            if ($factory->getName() === $name) {
                return true;
            }
        }

        return false;
    }
}
