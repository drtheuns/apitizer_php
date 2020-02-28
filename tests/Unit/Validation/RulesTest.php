<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\Rules;
use Apitizer\Validation\ObjectRules;

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

    /** @test */
    public function it_resolves_a_single_builder_when_only_one_is_requested()
    {
        $rules = new Rules();
        $rules->storeRules(function (ObjectRules $builder) {});
        $rules->updateRules(function (ObjectRules $builder) {
            $this->fail("The update rules should not be resolved");
        });

        $rules->rules('store');

        // Pass the test, since "fail" was never called.
        // We cant use "expectNotToPerformAssertions" because those tests are
        // removed from coverage.
        $this->addToAssertionCount(1);
    }

    /** @test */
    public function it_only_resolves_rules_once_for_each_action()
    {
        $count = 0;
        $rules = new Rules();
        $rules->storeRules(function (ObjectRules $builder) use (&$count) {
            if (++$count > 1) {
                $this->fail();
            }
        });

        // Call it twice.
        $rules->rules('store');
        $rules->rules('store');

        // Pass the test, since "fail" was never called.
        // We cant use "expectNotToPerformAssertions" because those tests are
        // removed from coverage.
        $this->addToAssertionCount(1);
    }
}
