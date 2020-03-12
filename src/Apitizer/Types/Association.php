<?php

namespace Apitizer\Types;

use Apitizer\Policies\PolicyFailed;
use Apitizer\Schema;
use Apitizer\Rendering\Renderer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Association extends Factory
{
    use Concerns\FetchesValueFromRow,
        Concerns\HasPolicy;

    /**
     * @var string The key of this association on the data source.
     */
    protected $key;

    /**
     * @var null|AbstractField[] The fields to render on the related query
     * schema.
     */
    protected $fields;

    /**
     * @var null|Association[]
     */
    protected $associations;

    /**
     * @var Schema the schema that renders the associated data.
     */
    protected $relatedSchema;

    public function __construct(
        Schema $declaredSchema,
        Schema $relatedSchema,
        string $key
    ) {
        parent::__construct($declaredSchema);
        $this->relatedSchema = $relatedSchema;
        $this->key = $key;
    }

    /**
     * @return AbstractField[]
     */
    public function getFields(): ?array
    {
        return $this->fields;
    }

    /**
     * @param AbstractField[] $fields
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return Association[]
     */
    public function getAssociations(): ?array
    {
        return $this->associations;
    }

    /**
     * @param Association[] $associations
     */
    public function setAssociations(array $associations): self
    {
        $this->associations = $associations;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Check if this association returns a collection of related rows.
     */
    public function returnsCollection(): bool
    {
        $model = $this->getSchema()->model();
        $relation = $model->{$this->key}();

        return ! $relation instanceof BelongsTo
            && ! $relation instanceof HasOne
            && ! $relation instanceof MorphOne;
    }

    public function getRelatedSchema(): Schema
    {
        return $this->relatedSchema;
    }
}
