<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * This class holds information about a single query builder and is intended to
 * be used to generate documentation.
 *
 * Each query builder has a callback `QueryBuilder::apidoc` where you can attach
 * extra information, such as a description or custom metadata, to the
 * documentation.
 */
class Apidoc
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var Assocation[]
     */
    protected $associations = [];

    /**
     * @var string A description of this resource.
     */
    protected $description;

    /**
     * @var mixed a free form data attribute that allows custom user data to be
     * attached to the documentation.
     */
    protected $metadata;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->setName($this->guessQueryBuilderResourceName());

        foreach ($queryBuilder->getFields() as $field) {
            if ($field instanceof Field) {
                $this->fields[] = $field;
            }

            if ($field instanceof Association) {
                $this->associations[] = $field;
            }
        }

        $queryBuilder->apidoc($this);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getAssociations(): array
    {
        return $this->associations;
    }

    public function getSorts(): array
    {
        return $this->queryBuilder->getSorts();
    }

    public function getFilters(): array
    {
        return $this->queryBuilder->getFilters();
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    protected function guessQueryBuilderResourceName()
    {
        // It might be better to guess based on the model's name.
        $className = (new ReflectionClass($this->queryBuilder))->getShortName();

        // UserBuilder -> User
        // UserQueryBuilder -> User
        \preg_match('/(.+?)(?:Query)?Builder$/', $className, $re);

        if (isset($re[1])) {
            return Str::title($re[1]);
        }

        return $className;
    }

    public function hasFilters(): bool
    {
        return ! empty($this->getFilters());
    }

    public function hasSorts(): bool
    {
        return ! empty($this->getSorts());
    }

    public function hasAssociations(): bool
    {
        return ! empty($this->getAssociations());
    }

    public function printAssociationType(Association $association): string
    {
        $name = $this->getName();

        return $association->returnsCollection()
            ? "array of $name"
            : $name;
    }

    /**
     * Get the metadata that was defined for this documentation.
     *
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * The metadata is a free form variable that can be filled with anything. If
     * you want to extend the documentation with your own metadata, this would
     * be the first place to look.
     */
    public function setMetadata($data): self
    {
        $this->metadata = $data;

        return $this;
    }
}
