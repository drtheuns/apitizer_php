<?php

namespace Tests\Feature\QueryBuilder;

use Apitizer\Policies\OwnerPolicy;
use Apitizer\Policies\Policy;
use Apitizer\Types\Field;
use Illuminate\Http\Request;
use Tests\Feature\Builders\EmptyBuilder;
use Tests\Feature\TestCase;
use Tests\Feature\Models\User;

class PolicyTest extends TestCase
{
    /** @test */
    public function the_owner_policy_allows_access_only_to_the_owner_of_the_resource()
    {
        $user = factory(User::class)->create();
        $fields = [
            'name' => $this->field('name')->policy(new OwnerPolicy('id'))
        ];

        $request = $this->request()->fields('name')->user($user)->make();
        $result = PolicyTestBuilder::new($request, $fields)->render($user);

        $this->assertEquals(['name' => $user->name], $result);

        $otherUser = factory(User::class)->create();
        $request = $this->request()->fields('name')->user($otherUser)->make();
        $result = PolicyTestBuilder::new($request, $fields)->render($user);

        $this->assertEmpty($result);

        $request = $this->request()->fields('name')->make();
        $result = PolicyTestBuilder::new($request, $fields)->render($user);

        $this->assertEmpty($result);
    }

    /** @test */
    public function the_owner_policy_can_use_keys_other_than_the_primary_key()
    {
        $fields = [
            'name' => $this->field('name')->policy(new OwnerPolicy('name', 'name'))
        ];
        $user = factory(User::class)->create();

        $request = $this->request()->fields('name')->user($user)->make();
        $result = PolicyTestBuilder::new($request, $fields)->render($user);

        $this->assertEquals(['name' => $user->name], $result);

        $otherUser = factory(User::class)->create();
        $request = $this->request()->fields('name')->user($otherUser)->make();
        $result = PolicyTestBuilder::new($request, $fields)->render($user);

        $this->assertEmpty($result);
    }

    /** @test */
    public function it_can_test_against_many_policies_to_find_one_that_passes()
    {
        $fields = [
            'name' => $this->field('name')->policyAny(new FalseP, new FalseP, new TrueP),
        ];
        $user = factory(User::class)->create();
        $request = $this->request()->fields('name')->make();
        $result = PolicyTestBuilder::new($request, $fields)->render($user);

        $this->assertEquals(['name' => $user->name], $result);

        $fields = [
            'name' => $this->field('name')->policyAny(new FalseP, new FalseP),
        ];

        $request = $this->request()->fields('name')->make();
        $result = PolicyTestBuilder::new($request, $fields)->render($user);

        $this->assertEmpty($result);
    }

    /** @test */
    public function it_can_test_against_multiple_groups_of_any_policies()
    {
        // Policy: (false or false or true) and (true) == true
        $fields = [
            'name' => $this->field('name')
                           ->policyAny(new FalseP, new FalseP, new TrueP)
                           ->policyAny(new TrueP),
        ];
        $user = factory(User::class)->create();
        $request = $this->request()->fields('name')->make();
        $result = PolicyTestBuilder::new($request, $fields)->render($user);

        $this->assertEquals(['name' => $user->name], $result);
    }

    private function field(string $name): Field
    {
        return (new Field(new EmptyBuilder, $name, 'string'))->setName($name);
    }
}

class TrueP implements Policy
{
    public function passes($value, $row, Field $field): bool
    {
        return true;
    }
}

class FalseP implements Policy
{
    public function passes($value, $row, Field $field): bool
    {
        return false;
    }
}

// Allows for arbitrary field definitions in tests.
class PolicyTestBuilder extends EmptyBuilder
{
    protected $fields;

    public function __construct(Request $request, $fields)
    {
        parent::__construct($request);
        $this->fields = collect($fields)->each->setQueryBuilder($this)->all();
    }

    public static function new(Request $request, array $fields)
    {
        return new static($request, $fields);
    }

    public function fields(): array
    {
        return $this->fields;
    }
}
