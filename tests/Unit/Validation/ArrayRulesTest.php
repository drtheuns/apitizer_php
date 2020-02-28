<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\ObjectRules;

class ArrayRulesTest extends TestCase
{
    /** @test */
    public function it_generates_correct_rules()
    {
        $this->assertRules([
            't1' => ['array', 'distinct'],
        ], function (ObjectRules $rules) {
            $rules->array('t1')->distinct();
        });
    }

    /** @test */
    public function it_nests_validation_rules()
    {
        $this->assertRules([
            't1'          => ['required', 'array'],
            't1.*'        => ['string', 'uuid'],
            't2'          => ['array'],
            't2.*'        => ['array'],
            't2.*.*'      => ['string'],
            't3'          => ['array'],
            't3.*'        => [],
            't3.*.id'     => ['string', 'uuid'],
            't3.*.tags'   => ['array'],
            't3.*.tags.*' => ['string', 'uuid'],
        ], function (ObjectRules $rules) {
            $rules->array('t1')->required()->whereEach()->uuid();
            $rules->array('t2')->whereEach()->array()->whereEach()->string();
            $rules->array('t3')->whereEach()->object(function (ObjectRules $rules) {
                $rules->uuid('id');
                $rules->array('tags')->whereEach()->uuid();
            });
        });
    }

    /** @test */
    public function it_supports_all_types_for_the_array_elements()
    {
        $this->assertRules([
            't1'         => ['array'],
            't1.*'       => ['string'],
            't2'         => ['array'],
            't2.*'       => ['string', 'uuid'],
            't3'         => ['array'],
            't3.*'       => ['boolean'],
            't4'         => ['array'],
            't4.*'       => ['date'],
            't5'         => ['array'],
            't5.*'       => ['date_format:' . DATE_ATOM],
            't6'         => ['array'],
            't6.*'       => ['numeric'],
            't7'         => ['array'],
            't7.*'       => ['integer'],
            't8'         => ['array'],
            't8.*'       => ['file'],
            't9'         => ['array'],
            't9.*'       => ['file', 'image'],
            't10'        => ['array'],
            't10.*'      => [],
            't10.*.name' => ['string'],
        ], function (ObjectRules $rules) {
            $rules->array('t1')->whereEach()->string();
            $rules->array('t2')->whereEach()->uuid();
            $rules->array('t3')->whereEach()->boolean();
            $rules->array('t4')->whereEach()->date();
            $rules->array('t5')->whereEach()->datetime();
            $rules->array('t6')->whereEach()->number();
            $rules->array('t7')->whereEach()->integer();
            $rules->array('t8')->whereEach()->file();
            $rules->array('t9')->whereEach()->image();
            $rules->array('t10')->whereEach()->object(function (ObjectRules $rules) {
                $rules->string('name');
            });
        });
    }
}
