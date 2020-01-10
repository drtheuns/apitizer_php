<?php

namespace Tests\Unit\Types;

use Apitizer\QueryBuilder;
use Apitizer\Types\Apidoc;
use Tests\Feature\Builders\EmptyBuilder;
use Tests\Feature\Builders\UserBuilder;
use Tests\Unit\TestCase;

class ApidocTest extends TestCase
{
    // The bindings are needed for this test.
    protected function getPackageProviders($app)
    {
        return ['Apitizer\ServiceProvider'];
    }

    /** @test */
    public function it_should_guess_resource_names_from_the_query_builder()
    {
        // UserBuilder -> User
        $onlyBuilder = new Apidoc(new UserBuilder());
        $this->assertEquals('User', $onlyBuilder->getName());

        // UserQueryBuilder -> User
        $queryBuilder = new Apidoc(new UserQueryBuilder());
        $this->assertEquals('User', $queryBuilder->getName());
    }

    /** @test */
    public function it_falls_back_to_the_class_name_if_it_cannot_guess_the_name()
    {
        $apidoc = new Apidoc(new NonBuilderName());
        $this->assertEquals('NonBuilderName', $apidoc->getName());
    }
}

// Used as examples for name guessing.
class UserQueryBuilder extends EmptyBuilder {}
class NonBuilderName extends EmptyBuilder {}
