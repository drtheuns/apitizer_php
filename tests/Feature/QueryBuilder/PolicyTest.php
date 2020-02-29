<?php

namespace Tests\Feature\QueryBuilder;

use Apitizer\Policies\CachedPolicy;
use Apitizer\Policies\OwnerPolicy;
use Apitizer\Policies\Policy;
use Apitizer\QueryBuilder;
use Apitizer\Types\Association;
use Apitizer\Types\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Tests\Support\Builders\EmptyBuilder;
use Tests\Support\Builders\PostBuilder;
use Tests\Feature\Models\Post;
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

    /** @test */
    public function policies_can_easily_be_cached_and_shared_amongst_fields()
    {
        // The same policy used in both fields, but should only be executed once.
        $policy = new CountCalledPolicy();
        $cachedPolicy = new CachedPolicy($policy);
        $fields = [
            'id' => $this->field('id', 'int')->policy($cachedPolicy),
            'name' => $this->field('name')->policy($cachedPolicy),
        ];
        $user = factory(User::class)->create();
        $request = $this->request()->fields('id,name')->make();
        $result = PolicyTestBuilder::new($request, $fields)->render($user);

        $this->assertEquals($user->only('id', 'name'), $result);
        $this->assertEquals(1, $policy->called);
    }

    /** @test */
    public function policies_are_applied_to_single_value_associations()
    {
        $post = factory(Post::class)->create();
        $request = $this->request()->fields('id,author(id)')->make();
        $result = PolicyPostBuilder::make($request)->render($post);

        $this->assertEquals($post->only('id'), $result);
    }

    /** @test */
    public function policies_are_applied_to_the_fields_in_an_association()
    {
        $user = factory(User::class)->state('withPosts')->create();
        $request = $this->request()->fields('id,posts(id,name)')->make();
        $result = PolicyUserBuilder::make($request)->render($user);

        $this->assertEquals([
            'id' => $user->id,
            'posts' => $user->posts->map->only('id')->all(),
        ], $result);
    }

    private function field(string $name, string $type = 'string'): Field
    {
        return (new Field(new EmptyBuilder, $name, $type))->setName($name);
    }

    private function association(string $name, QueryBuilder $builder): Association
    {
        return (new Association(new EmptyBuilder(), $builder, $name))
            ->setName($name);
    }
}

class TrueP implements Policy
{
    public function passes($value, $row, $fieldOrAssoc): bool
    {
        return true;
    }
}

class FalseP implements Policy
{
    public function passes($value, $row, $fieldOrAssoc): bool
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

class CountCalledPolicy implements Policy
{
    public $called = 0;
    public $retval;

    public function __construct($retval = true)
    {
        $this->retval = $retval;
    }

    public function passes($value, $row, $fieldOrAssoc): bool
    {
        $this->called++;
        return $this->retval;
    }
}

class FailId implements Policy
{
    protected $id;

    public function __construct($idToFail)
    {
        $this->id = $idToFail;
    }

    public function passes($value, $row, $fieldOrAssoc): bool
    {
        assert($fieldOrAssoc instanceof Association);

        // Fail a specific id.
        return $value['id'] !== $this->id;
    }
}

class PolicyUserBuilder extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'id' => $this->int('id'),
        ];
    }

    public function associations(): array
    {
        return [
            'posts' => $this->association('posts', PolicyPostBuilder::class),
        ];
    }
}

class PolicyPostBuilder extends EmptyBuilder
{
    public function fields(): array
    {
        return [
            'id' => $this->int('id'),
            'title' => $this->string('name')->policy(new FalseP),
        ];
    }

    public function associations(): array
    {
        return [
            'author' => $this->association('author', PolicyUserBuilder::class)
                ->policy(new FalseP),
        ];
    }

    public function model(): Model
    {
        return new Post();
    }
}
