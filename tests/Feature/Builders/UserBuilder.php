<?php

namespace Tests\Feature\Builders;

use Apitizer\QueryBuilder;
use Apitizer\Sorting\ColumnSort;
use Apitizer\Filters\AssociationFilter;
use Tests\Feature\Models\User;

class UserBuilder extends QueryBuilder
{
    public function fields(): array
    {
        return [
            'id'    => $this->int('id'),
            'name'  => $this->string('name'),
            'email' => $this->string('email'),
            'posts' => $this->association('posts', PostBuilder::class),
        ];
    }

    public function filters(): array
    {
        return [
            'posts' => $this->filter()
                            ->expectMany('string')
                            ->handleUsing(new AssociationFilter('posts', 'id')),
        ];
    }

    public function sorts(): array
    {
        return [
            'id' => new ColumnSort(),
        ];
    }

    public function datasource()
    {
        return User::query();
    }
}
