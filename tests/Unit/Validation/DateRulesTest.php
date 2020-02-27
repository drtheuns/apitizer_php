<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\ObjectRules;
use Carbon\Carbon;

class DateRulesTest extends TestCase
{
    /** @test */
    public function it_generates_correct_rules()
    {
        $this->assertRules([
            't1' => ['date'],
            't2' => ['date_format:' . DATE_ATOM],
        ], function (ObjectRules $rules) {
            $rules->date('t1');
            $rules->datetime('t2');
        });
    }

    /** @test */
    public function it_validates_after()
    {
        $tomorrow = Carbon::tomorrow();

        $this->assertRules([
            't1' => ['date', 'after:' . $tomorrow->format('Y-m-d')],
            't2' => ['date_format:'.DATE_ATOM, 'after:' . $tomorrow->format(DATE_ATOM)],
        ], function (ObjectRules $rules) use ($tomorrow) {
            $rules->date('t1')->after($tomorrow);
            $rules->datetime('t2')->after($tomorrow);
        });
    }

    /** @test */
    public function it_validates_after_or_equal()
    {
        $tomorrow = Carbon::tomorrow();

        $this->assertRules([
            't1' => ['date', 'after_or_equal:' . $tomorrow->format('Y-m-d')],
            't2' => ['date_format:' . DATE_ATOM, 'after_or_equal:' . $tomorrow->format(DATE_ATOM)],
        ], function (ObjectRules $rules) use ($tomorrow) {
            $rules->date('t1')->afterOrEqual($tomorrow);
            $rules->datetime('t2')->afterOrEqual($tomorrow);
        });
    }

    /** @test */
    public function it_validates_before()
    {
        $tomorrow = Carbon::tomorrow();

        $this->assertRules([
            't1' => ['date', 'before:' . $tomorrow->format('Y-m-d')],
            't2' => ['date_format:' . DATE_ATOM, 'before:' . $tomorrow->format(DATE_ATOM)],
        ], function (ObjectRules $rules) use ($tomorrow) {
            $rules->date('t1')->before($tomorrow);
            $rules->datetime('t2')->before($tomorrow);
        });
    }

    /** @test */
    public function it_validates_before_or_equal()
    {
        $tomorrow = Carbon::tomorrow();

        $this->assertRules([
            't1' => ['date', 'before_or_equal:' . $tomorrow->format('Y-m-d')],
            't2' => ['date_format:' . DATE_ATOM, 'before_or_equal:' . $tomorrow->format(DATE_ATOM)],
        ], function (ObjectRules $rules) use ($tomorrow) {
            $rules->date('t1')->beforeOrEqual($tomorrow);
            $rules->datetime('t2')->beforeOrEqual($tomorrow);
        });
    }

    /** @test */
    public function it_validates_the_equals_rule()
    {
        $this->assertRules([
            't1' => ['date', 'date_equals:2020-01-01'],
        ], function (ObjectRules $rules) {
            $rules->date('t1')->equals(Carbon::parse('2020-01-01'));
        });
    }
}
