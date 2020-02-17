<?php

namespace Tests\Feature\QueryBuilder;

use Tests\Support\Builders\EmptyBuilder;
use Tests\Feature\TestCase;
use Tests\Support\Builders\PostBuilder;
use Tests\Support\Builders\UserBuilder;
use Tests\Feature\Models\User;
use Tests\Feature\Models\Post;
use Tests\Feature\Models\Comment;

class SelectTest extends TestCase
{
        /** @test */
    public function it_can_select_the_specified_fields()
    {
        $post = factory(Post::class)->create();

        $request = $this->request()->fields('id,title,status')->make();
        $results = PostBuilder::make($request)->all();

        $this->assertEquals([$post->only('id', 'title', 'status')], $results);
    }

    /** @test */
    public function it_can_render_nested_selects()
    {
        $users = factory(User::class, 2)
               ->create()
               ->each(function (User $user) {
                   $posts = factory(Post::class, 2)
                          ->make(['author_id' => $user->id])
                          ->all();
                   $posts = $user->posts()->saveMany($posts);

                   collect($posts)->each(function (Post $post) {
                       $comments = factory(Comment::class, 2)
                                 ->make(['author_id' => $post->author_id])
                                 ->all();
                       $post->comments()->saveMany($comments);
                   });
               });

        $request = $this->request()->fields('id,name,posts(id,title,comments(id,body))')->make();
        $result = UserBuilder::make($request)->all();

        $expected = $users->map(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'posts' => $user->posts->map(function (Post $post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'comments' => $post->comments->map->only('id', 'body')->all(),
                    ];
                })->all(),
            ];
        })->all();

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function if_no_fields_are_selected_all_non_association_fields_are_returned()
    {
        $user = factory(User::class)->create();
        $request = $this->request()->make();
        $result = UserBuilder::make($request)->all();

        $expected = [
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'should_reset_password' => $user->should_reset_password,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d'),
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function if_no_valid_fields_are_selected_in_an_association_all_fields_are_returned()
    {
        $post = factory(Post::class)->state('withComments')->create();
        // comments doesn't have an 'authors' assoc/field.
        $request = $this->request()->fields('id,comments(authors)')->make();
        $result = PostBuilder::make($request)->render($post);

        $this->assertEquals([
            'id' => $post->id,
            'comments' => $post->comments->map->only('id', 'body')->all(),
        ], $result);
    }

    /** @test */
    public function selecting_an_association_without_specifying_fields_fetches_all_fields()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create(['author_id' => $user->id]);
        $comment = factory(Comment::class)->make(['author_id' => $user->id]);
        $post->comments()->save($comment);

        $request = $this->request()->fields('id,comments')->make();
        $result = PostBuilder::make($request)->all();

        $this->assertEquals([
            [
                'id' => $post->id,
                'comments' => [$comment->only('id', 'body')],
            ]
        ], $result);
    }

    /** @test */
    public function it_can_select_from_belongs_to_relations()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create(['author_id' => $user->id]);

        $request = $this->request()->fields('id, author(id)')->make();
        $result = PostBuilder::make($request)->all();

        $this->assertEquals([
            [
                'id' => $post->id,
                'author' => $user->only('id'),
            ]
        ], $result);
    }

    /** @test */
    public function it_can_handle_unexpected_array_fields()
    {
        $user = factory(User::class)->create();

        $request = $this->request()->fields(['id', 'posts(id)', 'posts' => ['id']])->make();
        $result = UserBuilder::make($request)->all();

        $this->assertEquals([
            [
                'id' => $user->id,
            ]
        ], $result);
    }

    /** @test */
    public function it_selects_from_morph_relationships()
    {
        $post = factory(Post::class)->state('withTags')->create();
        $request = $this->request()->fields('id, tags(id)')->make();
        $result = PostBuilder::make($request)->all();

        $this->assertEquals([
            [
                'id' => $post->id,
                'tags' => $post->tags->map->only('id')->all(),
            ],
        ], $result);
    }

    /** @test */
    public function it_loads_the_alwaysLoadColumns_columns()
    {
        $user = factory(User::class)->state('withPosts')->create();
        $request = $this->request()->fields('id,posts(id)')->make();
        $builder = LoadColumn::make($request)->buildQuery();

        $this->assertTrue(in_array('name', $builder->getQuery()->columns));

        $user = $builder->find($user->id);

        $this->assertNotNull($user->posts->first()->status);
    }

    /** @test */
    public function it_always_loads_the_primary_key()
    {
        $builder = EmptyBuilder::build();
        $this->assertEquals(['id'], $builder->getQuery()->columns);
    }
}

class LoadColumn extends EmptyBuilder
{
    protected $alwaysLoadColumns = ['name'];

    public function fields(): array
    {
        return [
            'id' => $this->int('id'),
            'posts' => $this->association('posts', LoadRelatedColumn::class),
        ];
    }
}

class LoadRelatedColumn extends EmptyBuilder
{
    protected $alwaysLoadColumns = ['status'];

    public function fields(): array
    {
        return [
            'id' => $this->int('id'),
        ];
    }
}
