<?php

namespace Tests\Feature\Schema;

use Illuminate\Database\Eloquent\Builder;
use Tests\Feature\TestCase;
use Tests\Support\Schemas\UserSchema;

class BuilderTest extends TestCase
{
    /** @test */
    public function the_schema_can_be_used_to_build_queries()
    {
        $request = $this->request()->fields('id, name')->make();

        $query = UserSchema::build($request);

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertCount(2, $query->getQuery()->columns);
    }

    /** @test */
    public function queries_can_be_build_according_to_manual_spec()
    {
        $query = UserSchema::make()->fromSpecification([
            'fields' => 'id, name',
        ])->buildQuery();

        $this->assertCount(2, $query->getQuery()->columns);
        $this->assertEquals(['id', 'name'], $query->getQuery()->columns);
    }
}
