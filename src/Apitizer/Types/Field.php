<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;

/**
 * A field that receives it's values from a model.
 */
class Field extends AbstractField
{
    /**
     * @var string The key that this field occupies on the data source.
     */
    protected $key;

    public function __construct(
        QueryBuilder $queryBuilder,
        string $key,
        string $type
    ) {
        parent::__construct($queryBuilder);
        $this->key = $key;
        $this->type = $type;
    }

    /**
     * @param mixed $row
     *
     * @return mixed
     */
    protected function getValue($row)
    {
        return $this->valueFromRow($row, $this->getKey());
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
