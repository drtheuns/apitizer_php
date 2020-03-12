<?php

namespace Tests\Unit\Rendering;

use Apitizer\Exceptions\InvalidOutputException;
use Apitizer\JsonApi\ResourceContainer;
use Apitizer\Schema;
use Apitizer\Rendering\JsonApiRenderer;
use Apitizer\Types\FetchSpec;
use Illuminate\Support\Arr;
use Tests\Feature\Models\User;
use Tests\Support\Schemas\EmptySchema;
use Tests\Support\Schemas\PostSchema;
use Tests\Support\Schemas\UserSchema;
use Tests\Unit\TestCase;

class JsonApiRendererTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withFactories(__DIR__ . '/../../Feature/database/factories');
    }

    /** @test */
    public function it_renders_a_resource_correctly(): void
    {
        $input = new ResourceContainer('user', '1', ['name' => 'John Doe']);
        $schema = new UserSchema;
        $renderer = new JsonApiRenderer;

        $actual = $renderer->renderSingleRow(
            $input,
            $schema,
            $this->fields($schema, ['name']),
            []
        );

        $expected = [
            'type' => 'user',
            'id' => $input->getResourceId(),
            'attributes' => [
                'name' => $input['name'],
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_renders_association_references(): void
    {
        $input = new ResourceContainer('post', '1', [
            'title' => 'Hello Test',
            'author' => new ResourceContainer('user', '1', ['name' => 'John Doe'])
        ]);
        $schema = new PostSchema;
        $renderer = new JsonApiRenderer;

        $actual = $renderer->renderSingleRow(
            $input,
            $schema,
            $this->fields($schema, ['title']),
            $this->associations($schema, ['author'])
        );

        $expected = [
            'type' => 'post',
            'id' => $input->getResourceId(),
            'attributes' => [
                'title' => $input['title'],
            ],
            'relationships' => [
                'author' => [
                    'data' => [
                        'id' => '1',
                        'type' => 'user',
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_renders_single_rows_of_data(): void
    {
        $input = new ResourceContainer('post', '1', [
            'title' => 'Hello Test',
            'author' => new ResourceContainer('user', '1', ['name' => 'John Doe'])
        ]);
        $postSchema = new PostSchema;
        $userSchema = new UserSchema;
        $associations = $this->associations($postSchema, ['author']);
        $associations['author']->setFields($this->fields($userSchema, ['name']));

        $fetchSpec = new FetchSpec(
            $this->fields($postSchema, ['title']),
            $associations
        );

        $actual = (new JsonApiRenderer)->render($postSchema, $input, $fetchSpec);

        $expected = [
            'data' => [
                'type' => 'post',
                'id' => $input->getResourceId(),
                'attributes' => [
                    'title' => $input['title'],
                ],
                'relationships' => [
                    'author' => [
                        'data' => [
                            'id' => '1',
                            'type' => 'user',
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'type' => 'user',
                    'id' => '1',
                    'attributes' => [
                        'name' => 'John Doe',
                    ],
                ]
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_renders_lists_of_data(): void
    {
        $input = new ResourceContainer('post', '1', [
            'title' => 'Hello Test',
            'author' => new ResourceContainer('user', '1', ['name' => 'John Doe'])
        ]);
        $postSchema = new PostSchema;
        $userSchema = new UserSchema;
        $associations = $this->associations($postSchema, ['author']);
        $associations['author']->setFields($this->fields($userSchema, ['name']));

        $fetchSpec = new FetchSpec(
            $this->fields($postSchema, ['title']),
            $associations
        );

        $actual = (new JsonApiRenderer)->render($postSchema, [$input], $fetchSpec);

        $expected = [
            'data' =>
            [
                [
                    'type' => 'post',
                    'id' => $input->getResourceId(),
                    'attributes' => [
                        'title' => $input['title'],
                    ],
                    'relationships' => [
                        'author' => [
                            'data' => [
                                'id' => '1',
                                'type' => 'user',
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'type' => 'user',
                    'id' => '1',
                    'attributes' => [
                        'name' => 'John Doe',
                    ],
                ]
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_throws_if_it_cannot_generate_an_identifier(): void
    {
        $this->expectException(InvalidOutputException::class);

        $input = ['title' => 'Fail me'];
        $schema = new EmptySchema;
        $actual = (new JsonApiRenderer)->renderSingleRow($input, $schema, [], []);
    }

    private function fields(Schema $schema, array $fields)
    {
        return Arr::only($schema->getFields(), $fields);
    }

    private function associations(Schema $schema, array $associations)
    {
        return Arr::only($schema->getAssociations(), $associations);
    }
}
