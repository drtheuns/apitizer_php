<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;
use Illuminate\Support\Str;
use ReflectionClass;

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
     * A description of this resource.
     *
     * @var string
     */
    protected $description;

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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getAssociations()
    {
        return $this->associations;
    }

    public function getSorts()
    {
        return $this->queryBuilder->getSorts();
    }

    public function getFilters()
    {
        return $this->queryBuilder->getFilters();
    }

    public function getQueryBuilder()
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

    public function printAssociationType(Association $association)
    {
        $name = $this->getName();

        return $association->returnsCollection()
            ? "array of $name"
            : $name;
    }
}
