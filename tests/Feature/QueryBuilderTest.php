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

    /** @test */
    public function it_can_order_results()
    {
        $users = factory(User::class, 2)->create();

        $request = $this->buildRequest([
            'sort' => 'id.desc',
            'fields' => 'id,name'
        ]);
        $result = (new UserBuilder($request))->build();

        $expected = [
            [
                'id' => $users[1]->id,
                'name' => $users[1]->name,
            ],
            [
                'id' => $users[0]->id,
                'name' => $users[0]->name,
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function if_no_fields_are_selected_all_non_association_fields_are_returned()
    {
        $user = factory(User::class)->create();
        $request = $this->buildRequest();
        $result = (new UserBuilder($request))->build();

        $expected = [
            [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        $this->assertEquals($expected, $result);
    }
}
