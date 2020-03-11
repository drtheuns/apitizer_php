<?php

namespace Tests\Support\Builders;

use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Models\Comment;

class CommentBuilder extends EmptyBuilder
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
            'author' => $this->association('author', UserBuilder::class),
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
