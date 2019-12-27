<?php

namespace Apitizer\Filters;

class LikeFilter
{
    protected $fields;

    public function __construct($fields)
    {
        $this->fields = is_array($fields) ? $fields : [$fields];
    }

    public function __invoke(Builder $query, string $value)
    {
        $searchTerm = '%' . $value . '%';

        $query->where(function ($query) use ($searchTerm) {
            foreach ($this->fields as $field) {
                $query->orWhere($field, 'like', $searchTerm);
            }
        });
    }
}
