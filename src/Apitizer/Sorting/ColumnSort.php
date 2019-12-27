<?php

namespace Apitizer\Sorting;

use Illuminate\Database\Eloquent\Builder;
use Apitizer\Types\Sort;

class ColumnSort
{
    protected $column;

    public function __construct(string $column = null)
    {
        $this->column = $column;
    }

    public function __invoke(Builder $query, Sort $sort)
    {
        $query->orderBy($this->column, $sort->getOrder());
    }
}
