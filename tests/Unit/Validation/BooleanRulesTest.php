<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\ObjectRules;

class BooleanRulesTest extends TestCase
{
    /** @test */
    public function it_generates_correct_rules()
    {
        $this->assertRules([
            't1' => ['required', 'boolean'],
            't2' => ['boolean', 'accepted'],
        ], function (ObjectRules $rules) {
            $rules->boolean('t1')->required();
            $rules->boolean('t2')->accepted();
        });
    }
}
