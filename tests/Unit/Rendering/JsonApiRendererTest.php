<?php

namespace Tests\Unit\Rendering;

use Apitizer\JsonApi\ResourceContainer;
use Apitizer\Rendering\JsonApiRenderer;
use Apitizer\Types\Field;
use Tests\Support\Builders\EmptyBuilder;
use Tests\Unit\TestCase;

class JsonApiRendererTest extends TestCase
{
    /** @test */
    public function it_renders_a_single_json_api_resource()
    {
        $user = new ResourceContainer('1', 'user', $attributes = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);

        $rendered = $this->renderOne($user, ['name', 'email']);

        $this->assertEquals([
            'id' => '1',
            'type' => 'user',
            'attributes' => $attributes,
        ], $rendered);
    }

    /** @test */
    public function it_renders_many_json_api_resources()
    {
        $attributes = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];
        $users = [];
        $users[] = new ResourceContainer('1', 'user', $attributes);
        $users[] = new ResourceContainer('2', 'user', $attributes);

        $rendered = $this->renderMany($users, ['name', 'email']);

        $this->assertEquals(
            [
                [
                    'id' => '1',
                    'type' => 'user',
                    'attributes' => $attributes,
                ],
                [
                    'id' => '2',
                    'type' => 'user',
                    'attributes' => $attributes,
                ]
            ],
            $rendered
        );
    }

    private function renderMany($resource, array $fields)
    {
        $renderer = new JsonApiRenderer();
        $builder = new EmptyBuilder();
        $fields = $this->castFields($fields, $builder);

        return $renderer->renderMany($resource, $builder, $fields, []);
    }

    private function renderOne($resource, array $fields)
    {
        $renderer = new JsonApiRenderer();
        $builder = new EmptyBuilder();
        $fields = $this->castFields($fields, $builder);

        return $renderer->renderSingleRow($resource, $builder, $fields, []);
    }

    private function castFields(array $fields, $builder)
    {
        return collect($fields)->map(function (string $field) use ($builder) {
            return (new Field($builder, $field, 'string'))->setName($field);
        })->all();
    }
}
