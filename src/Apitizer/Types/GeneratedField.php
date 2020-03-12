<?php

namespace Apitizer\Types;

use Apitizer\Schema;
use Illuminate\Database\Eloquent\Model;

class GeneratedField extends AbstractField
{
    /**
     * @var callable generator.
     */
    protected $generator;

    /**
     * @param Schema $schema
     * @param string $type
     * @param callable $generator the callable that will generate the return
     * value. This callable will receive two parameters:
     * 1. The current row that is being rendered.
     * 2. The GeneratedField instance (this object).
     */
    public function __construct(Schema $schema, string $type, callable $generator)
    {
        parent::__construct($schema);
        $this->type = $type;
        $this->generator = $generator;
    }

    /**
     * @param array|Model|mixed $row
     *
     * @return mixed
     */
    protected function getValue($row)
    {
        return call_user_func($this->generator, $row, $this);
    }
}
