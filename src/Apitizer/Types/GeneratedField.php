<?php

namespace Apitizer\Types;

use Apitizer\QueryBuilder;

class GeneratedField extends AbstractField
{
    /**
     * @param callable generator.
     */
    protected $generator;

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $type
     * @param $callable the callable that will generate the return value. This
     * callable will receive two parameters:
     * 1. The current row that is being rendered.
     * 2. The GeneratedField instance (this object).
     */
    public function __construct(QueryBuilder $queryBuilder, string $type, callable $generator)
    {
        parent::__construct($queryBuilder);
        $this->type = $type;
        $this->generator = $generator;
    }

    protected function getValue($row)
    {
        return call_user_func($this->generator, $row, $this);
    }
}
