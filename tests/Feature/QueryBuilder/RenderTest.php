<?php

namespace Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Tests\Feature\Models\User;
use Tests\Feature\Builders\UserBuilder;

/**
 * The query builder can be used JUST to render data, rather than also fetching
 * it. That's what this class tests.
 */
class RenderTest extends TestCase
{
    /** @test */
    public function it_renders_existing_eloquent_models()
    {
        $user = factory(User::class)->create();
        $request = $this->request()->fields('id,name')->make();
        $result = UserBuilder::make($request)->render($user);

        $this->assertEquals($user->only('id', 'name'), $result);
    }

    /** @test */
    public function it_renders_an_array_of_data()
    {
        $data = ['id' => 1, 'name' => 'Name', 'email' => 'Email'];
        $request = $this->request()->fields('id,name')->make();
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
        $request = $this->request()->fields('name,email')->make();
        $result = UserBuilder::make($request)->render($users);

        $this->assertInstanceOf(Collection::class, $users);
        $this->assertEquals($users->map(function (array $user) {
            return Arr::only($user, ['name', 'email']);
        })->all(), $result);
    }

    /** @test */
    public function it_renders_objects()
    {
        $user = factory(User::class)->create();
        $request = $this->request()->fields('id,name')->make();
        $result = UserBuilder::make($request)->render((object) $user->only('id', 'name'));

        $this->assertEquals($user->only('id', 'name'), $result);
    }
}
