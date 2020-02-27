<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\ObjectRules;

class StringRulesTest extends TestCase
{
    /** @test */
    public function it_validates_simple_rules()
    {
        $this->assertRules([
            't1' => ['string', 'active_url'],
            't2' => ['string', 'alpha'],
            't3' => ['string', 'alpha_dash'],
            't4' => ['string', 'alpha_num'],
            't5' => ['string', 'ip'],
            't6' => ['string', 'ipv4'],
            't7' => ['string', 'ipv6'],
            't8' => ['string', 'json'],
            't9' => ['string', 'numeric'],
            't10' => ['string', 'timezone'],
            't11' => ['string', 'url'],
            't12' => ['string', 'uuid'],
            't13' => ['string', 'digits:5'],
            't14' => ['string', 'digits_between:5,10'],
            't15' => ['string', 'starts_with:name'],
            't16' => ['string', 'ends_with:name'],
            't17' => ['string', 'email:rfc'],
            't18' => ['string', 'email:spoof,filter,dns'],
        ], function (ObjectRules $rules) {
            $rules->string('t1')->activeUrl();
            $rules->string('t2')->alpha();
            $rules->string('t3')->alphaDash();
            $rules->string('t4')->alphaNum();
            $rules->string('t5')->ip();
            $rules->string('t6')->ipv4();
            $rules->string('t7')->ipv6();
            $rules->string('t8')->json();
            $rules->string('t9')->numeric();
            $rules->string('t10')->timezone();
            $rules->string('t11')->url();
            $rules->string('t12')->uuid();
            $rules->string('t13')->digits(5);
            $rules->string('t14')->digitsBetween(5, 10);
            $rules->string('t15')->startsWith(['name']);
            $rules->string('t16')->endsWith(['name']);
            $rules->string('t17')->email();
            $rules->string('t18')->email(['spoof', 'filter', 'dns']);
        });
    }
}
