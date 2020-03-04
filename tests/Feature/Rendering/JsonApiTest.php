<?php

namespace Tests\Feature\Rendering;

use Apitizer\Rendering\JsonApiRenderer;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Tests\Feature\Models\User;
use Tests\Feature\TestCase;
use Tests\Support\Builders\UserBuilder;

/**
 * The query builder can be used JUST to render data, rather than also fetching
 * it. That's what this class tests.
 */
class JsonApiTest extends TestCase
{
    /** @test */
    public function it_renders_existing_eloquent_model_to_jsonapi_format()
    {
        // need to do this because DB unreliable since it doesn't always delete entire DB before test
        $user = User::updateOrCreate(['id' => 1], [
            'name' => 'Daan Hage',
            'email' => 'daan@atabix.nl',
        ]);

        $request = $this->request()->fields('name, email')->make();
        $result = UserBuilder::make($request)->setRenderer(new JsonApiRenderer)->render($user);

        $this->assertJsonStringEqualsJsonFile(
            'tests/assets/jsonapi/simple_user.json',
            json_encode($result, JSON_PRETTY_PRINT)
        );
    }

    /** @test */
    public function it_renders_multiple_eloquent_users_to_jsonapi_format()
    {
        // need to do this because DB unreliable since it doesn't always delete entire DB before test
        $user = User::updateOrCreate(['id' => 1], [
            'name' => 'Daan Hage',
            'email' => 'daan@atabix.nl',
        ]);
        $user2 = User::updateOrCreate(['id' => 2], [
            'name' => 'Randall Theuns',
            'email' => 'randall@atabix.nl',
        ]);

        $collection = collect([$user, $user2]);

        $request = $this->request()->fields('name, email')->make();
        $result = UserBuilder::make($request)->setRenderer(new JsonApiRenderer)->render($collection);

        $this->assertJsonStringEqualsJsonFile(
            'tests/assets/jsonapi/multiple_users.json',
            json_encode($result, JSON_PRETTY_PRINT)
        );
    }
}
