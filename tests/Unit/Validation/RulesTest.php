<?php

namespace Apitizer\Validation;

use Tests\Unit\TestCase;
use Apitizer\Validation\Rules;

class RulesTest extends TestCase
{
    /** @test */
    public function it_defines_and_builds_objects()
    {
        $rules = new Rules();
        $rules->define('store', function (ObjectRules $rules) {
            $rules->string('name');
        });

        $storeRules = $rules->builders('store');

        $this->assertInstanceOf(ObjectRules::class, $storeRules);
        $this->assertCount(1, $storeRules->getChildren());
    }

    /** @test */
    public function it_returns_an_empty_object_when_none_is_defined()
    {
        $rules = new Rules();
        $object = $rules->builders('store');

        $this->assertInstanceOf(ObjectRules::class, $object);
        $this->assertEmpty($object->getChildren());
    }

    /** @test */
    public function it_resolves_all_builders_when_all_rules_are_requested()
    {
        $rules = new Rules();
        $rules->storeRules(function (ObjectRules $builder) {});
        $rules->updateRules(function (ObjectRules $builder) {});

        $rules = $rules->rules();

        $this->assertCount(2, $rules);
        $this->assertEquals([
            'store' => [],
            'update' => []
        ], $rules);
    }
}
