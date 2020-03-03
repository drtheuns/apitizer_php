<?php

namespace Tests\Support\Builders;

use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Models\User;

class UserBuilder extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'id'    => $this->int('id'),
            'name'  => $this->string('name'),
            'email' => $this->string('email'),
            'should_reset_password' => $this->boolean('should_reset_password'),
            'created_at' => $this->datetime('created_at')->format(),
            'updated_at' => $this->date('updated_at')->format(),
        ];
    }

    public function associations(): array
    {
        return [
            'posts' => $this->association('posts', PostBuilder::class),
        ];
    }

    public function filters(): array
    {
        return [
            'name'       => $this->filter()->expectMany('string')->string()->byField('name'),
            'created_at' => $this->filter()->expect()->datetime()->byField('created_at', '>'),
            'posts'      => $this->filter()->expectMany('string')->string()->byAssociation('posts', 'id'),
            'updated_at' => $this->filter()->expect()->datetime('d-m-Y')->byField('updated_at', '>'),
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
