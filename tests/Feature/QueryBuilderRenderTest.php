<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Tests\Feature\Models\User;
use Tests\Feature\Builders\UserBuilder;

/**
 * The query builder can be used JUST to render data, rather than also fetching
 * it. That's what this class tests.
 */
class QueryBuilderRenderTest extends TestCase
{
    /** @test */
    public function it_renders_existing_eloquent_models()
    {
        $user = factory(User::class)->create();
        $request = $this->buildRequest(['fields' => 'id,name']);
        $result = UserBuilder::make($request)->render($user);

        $this->assertEquals([
            'id'   => $user->id,
            'name' => $user->name,
        ], $result);
    }

    /** @test */
    public function it_renders_an_array_of_data()
    {
        $data = ['id' => 1, 'name' => 'Name', 'email' => 'Email'];
        $request = $this->buildRequest(['fields' => 'id,name']);
        $result = UserBuilder::make($request)->render($data);

        $this->assertEquals([
            'id'   => 1,
            'name' => 'Name',
        ], $result);
    }

    /** @test */
    public function it_renders_a_collection_of_data()
    {
        $users = factory(User::class, 2)->make()->map(function ($user) {
            return $user->toArray();
        });
        $request = $this->buildRequest(['fields' => 'name,email']);
        $result = UserBuilder::make($request)->render($users);

        $this->assertInstanceOf(Collection::class, $users);
        $this->assertEquals(
            $users->map(function ($user) {
                return ['name' => $user['name'], 'email' => $user['email']];
            })->all(),
            $result
        );
    }
}
