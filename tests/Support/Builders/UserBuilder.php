<?php

namespace Tests\Support\Builders;

use Apitizer\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Models\User;

class UserBuilder extends QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'    => $this->int('id'),
            'name'  => $this->string('name'),
            'email' => $this->string('email'),
            'created_at' => $this->datetime('created_at')->format(),
            'updated_at' => $this->date('updated_at')->format(),
            'posts' => $this->association('posts', PostBuilder::class),
        ];
    }

    public function filters(): array
    {
        return [
            'name'       => $this->filter()->byField('name')->expectMany('string'),
            'created_at' => $this->filter()->byField('created_at', '>')->expect('datetime'),
            'posts'      => $this->filter()->byAssociation('posts', 'id')->expectMany('string'),
        ];
    }

    public function sorts(): array
    {
        return [
            'id' => $this->sort()->byField('id'),
        ];
    }

    public function model(): Model
    {
        return new User();
    }
}
