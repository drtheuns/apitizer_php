<?php

namespace Tests\Feature\Builders;

use Apitizer\QueryBuilder;
use Tests\Feature\Models\Post;

class PostBuilder extends QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'    => $this->int('id'),
            'title' => $this->string('title'),
            'body'  => $this->string('body'),
            'comments' => $this->association('comments', CommentBuilder::class),
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

    public function datasource()
    {
        return Post::query();
    }
}
