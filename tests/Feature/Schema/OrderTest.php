<?php

namespace Tests\Feature\Schema;

use Tests\Feature\TestCase;
use Tests\Support\Schemas\UserSchema;
use Tests\Feature\Models\User;

class OrderTest extends TestCase
{
    /** @test */
    public function it_can_order_results()
    {
        $users = factory(User::class, 2)->create()->sortByDesc(function ($user) {
            return $user->id;
        })->values();

        $request = $this->request()->fields('id,name')->sort('id.desc')->make();
        $result = UserSchema::make($request)->all();

        $this->assertEquals($users->map->only('id', 'name')->all(), $result);
    }
}
