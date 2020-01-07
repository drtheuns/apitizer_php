<?php

namespace Tests\Feature\Builders;

use Apitizer\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Models\Post;

class PostBuilder extends QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'         => $this->int('id'),
            'title'      => $this->string('title'),
            'body'       => $this->any('body'),
            'status'     => $this->enum('status', ['published', 'draft', 'scrapped', 'another-status']),
            'author'     => $this->association('author', UserBuilder::class),
            'comments'   => $this->association('comments', CommentBuilder::class),
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
        return new Post();
    }
}
