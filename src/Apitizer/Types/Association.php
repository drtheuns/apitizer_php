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
     * @var null|array The fields to render on the related query builder.
     */
    protected $fields;

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

    public function render($row, Renderer $renderer)
    {
        $assocData = $this->valueFromRow($row, $this->getKey());

        if ($this->returnsCollection()) {
            foreach ($assocData as $key => $value) {
                if (! $this->passesPolicy($value, $row, $this)) {
                    $assocData[$key] = new PolicyFailed;
                }
            }
        } else {
            if (! $this->passesPolicy($assocData, $row, $this)) {
                return new PolicyFailed;
            }
        }

        return $renderer->render(
            $this->getRelatedQueryBuilder(), $assocData, $this->fields
        );
    }

    public function getFields(): ?array
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = $fields;

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
