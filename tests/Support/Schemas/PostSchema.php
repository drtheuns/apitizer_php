<?php

namespace Tests\Support\Schemas;

use Apitizer\Routing\Scope;
use Apitizer\Types\Apidoc;
use Illuminate\Database\Eloquent\Model;
use Tests\Feature\Models\Post;

class PostSchema extends EmptySchema
{
    const DESCRIPTION = 'A blog post';

    public function fields(): array
    {
        return [
            'id'       => $this->int('id'),
            'uuid'     => $this->uuid('uuid'),
            'title'    => $this->string('title'),
            'body'     => $this->any('body')->nullable(),
            'status'   => $this->enum('status', ['published', 'draft', 'scrapped', 'another-status']),
            'total'    => $this->generatedField('string', function ($row) {
                return \strlen($row->title);
            }),
        ];
    }

    public function associations(): array
    {
        return [
            'author'   => $this->association('author', UserSchema::class),
            'comments' => $this->association('comments', CommentSchema::class),
            'tags'     => $this->association('tags', TagSchema::class),
        ];
    }

    public function filters(): array
    {
        return [
            'search'   => $this->filter()->search('title'),
            'user'     => $this->filter()->byAssociation('author'),
            'userUuid' => $this->filter()->byAssociation('author', 'uuid'),
            'tag'      => $this->filter()->byAssociation('tags'),
            'tagUuid'  => $this->filter()->byAssociation('tags', 'uuid'),
        ];
    }

    public function sorts(): array
    {
        return [];
    }

    public function scope(Scope $scope)
    {
        $scope->crud()
              ->associationCrud('author')
              ->associationCrud('comments');
    }

    public function model(): Model
    {
        return new Post();
    }

    public function apidoc(Apidoc $apidoc): void
    {
        $apidoc->setDescription(self::DESCRIPTION);
    }
}
