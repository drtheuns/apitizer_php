<?php

namespace Apitizer\Sorting;

use Illuminate\Database\Eloquent\Builder;
use Apitizer\Types\Sort;

class ColumnSort
{
    public function __invoke(Builder $query, Sort $sort)
    {
        $query->orderBy($sort->getField(), $sort->getOrder());
    }
}
