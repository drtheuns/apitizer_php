<?php

namespace Apitizer\Types;

use Apitizer\Policies\PolicyFailed;
use Apitizer\QueryBuilder;
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
     * builder.
     */
    protected $fields;

    /**
     * @var null|Association[]
     */
    protected $associations;

    /**
     * @var QueryBuilder the query builder that renders the associated data.
     */
    protected $relatedBuilder;

    public function __construct(
        QueryBuilder $declaredBuilder,
        QueryBuilder $relatedBuilder,
        string $key
    ) {
        parent::__construct($declaredBuilder);
        $this->relatedBuilder = $relatedBuilder;
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
        $model = $this->getQueryBuilder()->model();
        $relation = $model->{$this->key}();

        return ! $relation instanceof BelongsTo
            && ! $relation instanceof HasOne
            && ! $relation instanceof MorphOne;
    }

    public function getRelatedQueryBuilder(): QueryBuilder
    {
        return $this->relatedBuilder;
    }
}
