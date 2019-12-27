<?php

namespace Tests\Feature\Builders;

use Apitizer\QueryBuilder;
use Tests\Feature\Models\Comment;

class CommentBuilder extends QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'    => $this->int('id'),
            'body' => $this->string('body'),
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

    public function model()
    {
        return new Comment();
    }
}
