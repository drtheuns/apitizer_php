<?php

namespace Tests\Feature\Rendering;

use Apitizer\Rendering\JsonApiRenderer;
use Tests\Feature\Models\Comment;
use Tests\Feature\Models\Post;
use Tests\Feature\Models\User;
use Tests\Feature\TestCase;
use Tests\Support\Builders\PostBuilder;

class JsonApiRendererTest extends TestCase
{
    /** @test */
    public function it_renders_paginated_data(): void
    {
        $author = factory(User::class)->create();
        $post = factory(Post::class)->create(['author_id' => $author->id]);
        $comment = factory(Comment::class)->create([
            'author_id' => $author->id,
            'post_id' => $post->id,
        ]);

        $request = $this->request()
                        ->fields('title,comments(body,author(name, comments(uuid,author(should_reset_password)))),'
                                 .'author(email, posts(title))')
                        ->make();
        $actual = PostBuilder::make($request)
                ->setRenderer(new JsonApiRenderer)
                ->paginate();

        $data = [
            [
                'type' => 'post',
                'id' => (string) $post->id,
                'attributes' => $post->only('title'),
                'relationships' => [
                    'comments' => [
                        'data' => [
                            [
                                'type' => 'comment',
                                'id' => (string) $comment->id,
                            ]
                        ]
                    ],
                    'author' => [
                        'data' => [
                            'type' => 'user',
                            'id' => (string) $author->id,
                        ]
                    ]
                ]
            ]
        ];

        $included = [
            [
                'type' => 'comment',
                'id' => (string) $comment->id,
                'attributes' => [
                    'body' => $comment->body,
                    'uuid' => (string) $comment->uuid,
                ],
                'relationships' => [
                    'author' => [
                        'data' => [
                            'type' => 'user',
                            'id' => (string) $author->id,
                        ]
                    ],
                ]
            ],
            [
                'type' => 'user',
                'id' => (string) $author->id,
                'attributes' => $author->only(['name', 'email', 'should_reset_password']),
                'relationships' => [
                    'comments' => [
                        'data' => [
                            [
                                'type' => 'comment',
                                'id' => (string) $comment->id,
                            ]
                        ]
                    ],
                    'posts' => [
                        'data' => [
                            [
                                'type' => 'post',
                                'id' => (string) $post->id,
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'post',
                'id' => (string) $post->id,
                'attributes' => $post->only('title'),
            ]
        ];

        $this->assertEquals($data, $actual['data']);
        $this->assertEquals($included, $actual['included']);
    }
}
