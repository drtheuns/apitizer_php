<?php

namespace Apitizer\Types;

use Apitizer\Schema;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * This class holds information about a single schema and is intended to
 * be used to generate documentation.
 *
 * Each schema has a callback `Schema::apidoc` where you can attach
 * extra information, such as a description or custom metadata, to the
 * documentation.
 */
class Apidoc
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string A description of this resource.
     */
    protected $description;

    /**
     * @var mixed a free form data attribute that allows custom user data to be
     * attached to the documentation.
     */
    protected $metadata;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
        $this->setName($this->guessSchemaResourceName());

        $schema->apidoc($this);
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

    /**
     * @return AbstractField[]
     */
    public function getFields(): array
    {
        return $this->schema->getFields();
    }

    /**
     * @return Association[]
     */
    public function getAssociations(): array
    {
        return $this->schema->getAssociations();
    }

    /**
     * @return Sort[]
     */
    public function getSorts(): array
    {
        return $this->schema->getSorts();
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->schema->getFilters();
    }

    /**
     * @return array<string, \Apitizer\Validation\ObjectRules>
     */
    public function getValidationBuilders(): array
    {
        return $this->schema->getRules()->getBuilders();
    }

    public function getSchema(): Schema
    {
        return $this->schema;
    }

    protected function guessSchemaResourceName(): string
    {
        // It might be better to guess based on the model's name.
        $className = (new ReflectionClass($this->schema))->getShortName();

        // UserSchema -> User
        // UserSchema -> User
        \preg_match('/(.+?)Schema$/', $className, $re);

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

    public function hasRules(): bool
    {
        return $this->getSchema()->getRules()->hasRules();
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
     *
     * @param mixed $data
     */
    public function setMetadata($data): self
    {
        $this->metadata = $data;

        return $this;
    }

    public function humanizeActionName(string $actionName): string
    {
        switch ($actionName) {
            case 'store':
                return 'Create';
            case 'destroy':
                return 'Delete';
            default:
                return Str::title($actionName);
        }
    }
}
