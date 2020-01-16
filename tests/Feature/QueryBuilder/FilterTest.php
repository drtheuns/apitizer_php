<?php

namespace Tests\Feature\QueryBuilder;

use Tests\Feature\TestCase;
use Tests\Support\Builders\PostBuilder;
use Tests\Support\Builders\UserBuilder;
use Tests\Feature\Models\User;
use Tests\Feature\Models\Post;

class FilterTest extends TestCase
{
    /** @test */
    public function it_can_filter_on_associations()
    {
        $users = factory(User::class, 2)->create();
        $post = factory(Post::class)->make();
        $users->first()->posts()->save($post);

        $request = $this->request()->fields('id')->filter('posts', [$post->id])->make();
        $result = UserBuilder::make($request)->all();

        $this->assertEquals([$users->first()->only('id')], $result);
    }

    /** @test */
    public function it_performs_like_filters_on_fields()
    {
        $post1 = factory(Post::class)->create(['title' => 'Hello world']);
        $post2 = factory(Post::class)->create(['title' => 'None']);
        $request = $this->request()->fields('id')->filter('search', 'Hello')->make();

        $result = PostBuilder::make($request)->all();

        $this->assertEquals([$post1->only('id')], $result);
    }

    /** @test */
    public function it_filters_on_belongs_to_associations()
    {
        $posts = factory(Post::class, 2)->create();
        $post = $posts->first();
        $request = $this->request()->fields('id')->filter('user', $post->author_id)->make();

        $result = PostBuilder::make($request)->all();

        $this->assertNotEquals($post->author_id, $posts[1]->author_id);
        $this->assertEquals([$post->only('id')], $result);
    }

    /** @test */
    public function the_association_filter_filters_on_keys_other_than_the_primary_key()
    {
        $posts = factory(Post::class, 2)->create();
        $post = $posts->first();
        $request = $this->request()->fields('id')->filter('userUuid', $post->author->uuid)->make();

        $result = PostBuilder::make($request)->all();

        $this->assertNotEquals($post->author_id, $posts[1]->author_id);
        $this->assertEquals([$post->only('id')], $result);
    }

    /** @test */
    public function it_filters_on_morph_relationships()
    {
        $posts = factory(Post::class, 2)->state('withTags')->create();
        $post = $posts->first();
        $tag = $post->tags->first();

        $request = $this->request()->fields('id')->filter('tag', $tag->id)->make();
        $result = PostBuilder::make($request)->all();

        $this->assertEquals([$post->only('id')], $result);
    }

    /** @test */
    public function it_filters_on_morph_relationships_with_non_primary_keys()
    {
        $posts = factory(Post::class, 2)->state('withTags')->create();
        $post = $posts->first();
        $tag = $post->tags->first();

        $request = $this->request()->fields('id')->filter('tagUuid', $tag->uuid)->make();
        $result = PostBuilder::make($request)->all();

        $this->assertEquals([$post->only('id')], $result);
    }

    /** @test */
    public function it_can_filter_by_field()
    {
        $users = factory(User::class, 2)->create();
        $expectedUser = $users->first();

        $request = $this->request()
                        ->filter('name', [$expectedUser->name])
                        ->fields('id')
                        ->make();
        $result = UserBuilder::make($request)->all();

        $this->assertEquals([$expectedUser->only('id')], $result);
    }

    /** @test */
    public function it_filters_by_custom_operators()
    {
        $users = factory(User::class, 2)->create();
        $expectedUser = $users->first();
        $expectedUser->created_at = '2021-01-02 13:00:00';
        $expectedUser->save();

        $request = $this->request()
                        ->filter('created_at', '2021-01-01 00:00:00')
                        ->fields('id')
                        ->make();
        $result = UserBuilder::make($request)->all();

        $this->assertEquals([$expectedUser->only('id')], $result);
    }
}
