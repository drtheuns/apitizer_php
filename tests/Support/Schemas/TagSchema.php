<?php

namespace Tests\Support\Schemas;

use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Models\Tag;

class TagSchema extends EmptySchema
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
