<?php

namespace Tests\Support\Schemas;

use Apitizer\Schema;
use Apitizer\Routing\Scope;
use Apitizer\Validation\Rules;
use Tests\Feature\Models\User;
use Illuminate\Database\Eloquent\Model;

class EmptySchema extends Schema
{
    public function fields(): array
    {
        return [];
    }

    public function associations(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [];
    }

    public function sorts(): array
    {
        return [];
    }

    public function scope(Scope $scope)
    {
    }

    public function rules(Rules $rules)
    {
    }

    public function model(): Model
    {
        return new User();
    }
}
