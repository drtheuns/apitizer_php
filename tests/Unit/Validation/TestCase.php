<?php

namespace Tests\Unit\Validation;

use PHPUnit\Framework\Assert as PHPUnit;
use Apitizer\Validation\ObjectRules;
use Apitizer\Validation\RuleInterpreter;
use Closure;

class TestCase extends \Tests\Unit\TestCase
{
    public function assertRules(array $expected, Closure $builder)
    {
        $object = new ObjectRules(null, $builder);
        $object->resolve();
        $rules = RuleInterpreter::rulesFrom($object);

        PHPUnit::assertEquals(
            $expected,
            $rules,
            "The generated rules did not match the expected rules"
        );
    }
}
