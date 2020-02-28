<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\ObjectRules;

class NumberRulesTest extends TestCase
{
    /** @test */
    public function it_generates_correct_rules()
    {
        $this->assertRules([
            't1' => ['numeric'],
            't2' => ['integer'],
        ], function (ObjectRules $rules) {
            $rules->number('t1');
            $rules->integer('t2');
        });
    }

    /** @test */
    public function it_validates_starts_and_ends_with()
    {
        $this->assertRules([
            't1' => ['numeric', 'ends_with:name,wow'],
            't1' => ['numeric', 'starts_with:name,wow'],
        ], function (ObjectRules $rules) {
            $rules->number('t1')->endsWith(['name', 'wow']);
            $rules->number('t1')->startsWith(['name', 'wow']);
        });
    }

    /** @test */
    public function it_validates_digits()
    {
        $this->assertRules([
            't1' => ['numeric', 'digits:5'],
            't2' => ['numeric', 'digits_between:14,34'],
        ], function (ObjectRules $rules) {
            $rules->number('t1')->digits(5);
            $rules->number('t2')->digitsBetween(14, 34);
        });
    }
}
