<?php

namespace Apitizer\Types;

use Apitizer\Schema;

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
        Schema $schema,
        string $key,
        string $type
    ) {
        parent::__construct($schema);
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
