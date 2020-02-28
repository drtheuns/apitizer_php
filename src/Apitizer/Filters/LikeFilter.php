<?php

namespace Apitizer\Filters;

use Illuminate\Database\Eloquent\Builder;

class LikeFilter
{
    /**
     * @var string[]
     */
    protected $fields;

    /**
     * @param string|string[] $fields
     */
    public function __construct($fields)
    {
        $this->fields = is_array($fields) ? $fields : func_get_args();
    }

    public function __invoke(Builder $query, string $value): void
    {
        $searchTerm = '%' . $value . '%';

        $query->where(function ($query) use ($searchTerm) {
            foreach ($this->fields as $field) {
                $query->orWhere($field, 'like', $searchTerm);
            }
        });
    }
}
