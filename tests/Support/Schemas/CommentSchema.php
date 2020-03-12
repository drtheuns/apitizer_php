<?php

namespace Tests\Support\Schemas;

use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Models\Comment;

class CommentSchema extends EmptySchema
{
    public function fields(): array
    {
        return [
            'id'   => $this->int('id'),
            'uuid' => $this->uuid('uuid'),
            'body' => $this->string('body'),
        ];
    }

    public function associations(): array
    {
        return [
            'author' => $this->association('author', UserSchema::class),
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
        return new Comment();
    }
}
