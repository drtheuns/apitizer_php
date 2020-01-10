<?php

namespace Tests\Feature\QueryBuilder;

use Apitizer\Exceptions\InvalidInputException;
use Apitizer\ExceptionStrategy\Ignore;
use Tests\Feature\Builders\UserBuilder;
use Tests\Feature\TestCase;
use Tests\Feature\Models\User;

class ExceptionHandlingTest extends TestCase
{
    /** @test */
    public function the_raise_strategy_will_throw_on_invalid_input()
    {
        $this->expectException(InvalidInputException::class);

        $request = $this->request()->filter('name', 'expect array')->make();
        UserBuilder::make($request)->all();
    }

    /** @test */
    public function the_ignore_strategy_will_ignore_the_error_and_continue()
    {
        $users = factory(User::class, 2)->create();

        // 'name' filter expects array.
        $request = $this->request()
                        ->fields('id')
                        ->filter('name', $users->first()->name)
                        ->make();

        $result = UserBuilder::make($request)
                ->setExceptionStrategy(new Ignore)
                ->all();
        $expected = $users->map->only('id')->all();

        $this->assertEquals($expected, $result, 'filter was not applied');
    }

    /** @test */
    public function cast_errors_in_fields_can_be_ignored()
    {
        $request = $this->request()->fields('created_at')->make();
        $result = UserBuilder::make($request)
                ->setExceptionStrategy(new Ignore)
                ->render(['created_at' => 'hello world']);

        $this->assertEquals(['created_at' => null], $result);
    }
}
