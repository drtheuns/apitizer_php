<?php

namespace Tests\Support\Builders;

use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Models\Tag;

class TagBuilder extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'id'     => $this->int('id'),
            'name'   => $this->string('name'),
            'weight' => $this->float('weight'),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    public function sorts(): array
    {
        return [];
    }

    public function model(): Model
    {
        return new Tag();
    }
}
