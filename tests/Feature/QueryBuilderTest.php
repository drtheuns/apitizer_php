<?php

namespace Tests\Feature;

use Tests\Feature\Builders\UserBuilder;
use Tests\Feature\Models\User;
use Tests\Feature\Models\Post;
use Tests\Feature\Models\Comment;

class QueryBuilderTest extends TestCase
{
    /** @test */
    public function it_can_select_the_specified_fields()
    {
        $user = factory(User::class)->create();

        $request = $this->buildRequest(['fields' => 'id,name']);
        $results = (new UserBuilder($request))->build();

        $this->assertEquals([
            [
                'id' => $user->id,
                'name' => $user->name,
            ]
        ], $results);
    }

    /** @test */
    public function it_can_render_nested_selects()
    {
        $users = factory(User::class, 2)
               ->create()
               ->each(function (User $user) {
                   $posts = $user->posts()
                        ->saveMany(factory(Post::class, 2)->make()->all());

                   collect($posts)->each(function (Post $post) {
                       $comments = factory(Comment::class, 2)
                                 ->make(['author_id' => $post->author_id])
                                 ->all();
                       $post->comments()->saveMany($comments);
                   });
               });

        $request = $this->buildRequest(['fields' => 'id,name,posts(id,title,comments(id,body))']);
        $result = (new UserBuilder($request))->build();

        $expected = $users->map(function (User $user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'posts' => $user->posts->map(function (Post $post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'comments' => $post->comments->map(function (Comment $comment) {
                            return [
                                'id' => $comment->id,
                                'body' => $comment->body,
                            ];
                        })->all(),
                    ];
                })->all(),
            ];
        })->all();

        $this->assertEquals($expected, $result);
    }
}
