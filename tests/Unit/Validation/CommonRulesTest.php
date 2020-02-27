<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\ObjectRules;

class CommonRulesTest extends TestCase
{
    /** @test */
    public function it_generates_correct_validation_rules()
    {
        $this->assertRules([
            't1'  => ['string', 'required_with:t1,t2'],
            't2'  => ['string', 'required_with_all:t1,t2'],
            't3'  => ['string', 'required_without:t1,t2'],
            't4'  => ['string', 'required_without_all:t1,t2'],
            't5'  => ['string', 'nullable'],
            't6'  => ['string', 'size:50'],
            't7'  => ['string', 'max:50'],
            't8'  => ['string', 'min:20'],
            't9'  => ['string', 'bail'],
            't10' => ['string', 'confirmed'],
            't11' => ['string', 'between:5,10'],
            't12' => ['string', 'different:t1'],
            't13' => ['string', 'same:t1'],
            't14' => ['string', 'regex:/hello/'],
            't15' => ['string', 'not_regex:/hello/'],
            't16' => ['string', 'gt:t1'],
            't17' => ['string', 'gte:t1'],
            't18' => ['string', 'lt:t1'],
            't19' => ['string', 'lte:t1'],
            't20' => ['string', 'in_array:t1.*'],
            't21' => ['string', 'filled'],
            't22' => ['string', 'sometimes'],
            't23' => ['string', 'present'],
        ], function (ObjectRules $rules) {
            $rules->string('t1')->requiredWith(['t1', 't2']);
            $rules->string('t2')->requiredWithAll(['t1', 't2']);
            $rules->string('t3')->requiredWithout(['t1', 't2']);
            $rules->string('t4')->requiredWithoutAll(['t1', 't2']);
            $rules->string('t5')->nullable();
            $rules->string('t6')->size(50);
            $rules->string('t7')->max(50);
            $rules->string('t8')->min(20);
            $rules->string('t9')->bail();
            $rules->string('t10')->confirmed();
            $rules->string('t11')->between(5, 10);
            $rules->string('t12')->different('t1');
            $rules->string('t13')->same('t1');
            $rules->string('t14')->regex('/hello/');
            $rules->string('t15')->notRegex('/hello/');
            $rules->string('t16')->gt('t1');
            $rules->string('t17')->gte('t1');
            $rules->string('t18')->lt('t1');
            $rules->string('t19')->lte('t1');
            $rules->string('t20')->inArray('t1');
            $rules->string('t21')->filled();
            $rules->string('t22')->sometimes();
            $rules->string('t23')->present();
        });
    }
}
