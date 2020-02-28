<?php

namespace Apitizer\Sorting;

use Illuminate\Database\Eloquent\Builder;
use Apitizer\Types\Sort;

class ColumnSort
{
    /**
     * @var string
     */
    protected $column;

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    public function __invoke(Builder $query, Sort $sort): void
    {
        $query->orderBy($this->column, $sort->getOrder());
    }
}
