<?php

namespace Tests\Unit\Validation;

use Tests\Unit\TestCase;
use Apitizer\Validation\RuleBuilder;

class RuleBuilderTest extends TestCase
{
    /** @test */
    public function it_has_strongly_typed_builders()
    {
        $builder = new RuleBuilder();

        $this->assertEquals('string', $builder->string('name')->getType());
        $this->assertEquals('string', $builder->uuid('name')->getType());
        $this->assertEquals('boolean', $builder->boolean('name')->getType());
        $this->assertEquals('date', $builder->date('name')->getType());
        $this->assertEquals('datetime', $builder->datetime('name')->getType());
        $this->assertEquals('array', $builder->array('name')->getType());
        $this->assertEquals('number', $builder->number('name')->getType());
        $this->assertEquals('file', $builder->file('name')->getType());
        $this->assertEquals('file', $builder->image('name')->getType());
    }
}
