<?php

namespace Tests\Support\Schemas;

use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Models\User;

class UserSchema extends EmptySchema
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
            'posts' => $this->association('posts', PostSchema::class),
            'comments' => $this->association('comments', CommentSchema::class),
        ];
    }

    public function filters(): array
    {
        return [
            'name'       => $this->filter()->expect()->string()->byField('name'),
            'created_at' => $this->filter()->expect()->datetime()->byField('created_at', '>'),
            'posts'      => $this->filter()->expect()->array()->whereEach()->string()->byAssociation('posts', 'id'),
            'updated_at' => $this->filter()->expect()->datetime('d-m-Y')->byField('updated_at', '>'),
            'active'     => $this->filter()->expect()->boolean()->byField('active'),
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
