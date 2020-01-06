<?php

namespace Tests\Unit\Types;

use Apitizer\QueryBuilder;
use Apitizer\Types\Apidoc;
use Tests\Feature\Builders\UserBuilder;
use Tests\Feature\Models\User;
use Tests\Unit\TestCase;

class ApidocTest extends TestCase
{
    /** @test */
    public function it_should_guess_resource_names_from_the_query_builder()
    {
        // UserBuilder -> User
        $onlyBuilder = new Apidoc(new UserBuilder($this->buildRequest()));
        $this->assertEquals('User', $onlyBuilder->getName());

        // UserQueryBuilder -> User
        $queryBuilder = new Apidoc(new UserQueryBuilder($this->buildRequest()));
        $this->assertEquals('User', $queryBuilder->getName());
    }
}

// Used as an example for name guessing.
class UserQueryBuilder extends QueryBuilder
{
    public function fields(): array { return []; }
    public function filters(): array { return []; }
    public function sorts(): array { return []; }
    public function model() {
        return new User();
    }
}
