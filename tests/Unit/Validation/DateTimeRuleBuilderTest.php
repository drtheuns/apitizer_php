<?php

namespace Tests\Unit\Validation;

use Carbon\Carbon;

class DateTimeRuleBuilderTest extends TestCase
{
    /** @test */
    public function it_validates_after()
    {
        $tomorrow = Carbon::tomorrow();

        $this->builder()
             ->rules(function ($builder) use ($tomorrow) {
                 $builder->date('e1')->after('2020-01-01');
                 $builder->datetime('e2')->after('2020-01-01');
                 $builder->date('e3')->after($tomorrow);
                 $builder->datetime('e4')->after($tomorrow);
             })
             ->assertRulesFor('e1', ['date_format:Y-m-d', 'after:2020-01-01'])
             ->assertRulesFor('e2', ['date_format:' . DATE_RFC3339, 'after:2020-01-01'])
             ->assertRulesFor('e3', [
                 'date_format:Y-m-d', 'after:' . $tomorrow->format('Y-m-d')
             ])
             ->assertRulesFor('e4', [
                 'date_format:' . DATE_RFC3339, 'after:' . $tomorrow->format(\DATE_RFC3339)
             ]);
    }

    /** @test */
    public function it_validates_after_or_equal()
    {
        $tomorrow = Carbon::tomorrow();

        $this->builder()
            ->rules(function ($builder) use ($tomorrow) {
                $builder->date('e1')->afterOrEqual('2020-01-01');
                $builder->datetime('e2')->afterOrEqual('2020-01-01');
                $builder->date('e3')->afterOrEqual($tomorrow);
                $builder->datetime('e4')->afterOrEqual($tomorrow);
            })
            ->assertRulesFor('e1', [
                'date_format:Y-m-d',
                'after_or_equal:2020-01-01'
            ])
            ->assertRulesFor('e2', [
                'date_format:' . DATE_RFC3339,
                'after_or_equal:2020-01-01'
            ])
            ->assertRulesFor('e3', [
                'date_format:Y-m-d',
                'after_or_equal:' . $tomorrow->format('Y-m-d')
            ])
            ->assertRulesFor('e4', [
                'date_format:' . DATE_RFC3339,
                'after_or_equal:' . $tomorrow->format(\DATE_RFC3339)
            ]);
    }

    /** @test */
    public function it_validates_before()
    {
        $tomorrow = Carbon::tomorrow();

        $this->builder()
            ->rules(function ($builder) use ($tomorrow) {
                $builder->date('e1')->before('2020-01-01');
                $builder->datetime('e2')->before('2020-01-01');
                $builder->date('e3')->before($tomorrow);
                $builder->datetime('e4')->before($tomorrow);
            })
            ->assertRulesFor('e1', [
                'date_format:Y-m-d',
                'before:2020-01-01'
            ])
            ->assertRulesFor('e2', [
                'date_format:' . DATE_RFC3339,
                'before:2020-01-01'
            ])
            ->assertRulesFor('e3', [
                'date_format:Y-m-d',
                'before:' . $tomorrow->format('Y-m-d')
            ])
            ->assertRulesFor('e4', [
                'date_format:' . DATE_RFC3339,
                'before:' . $tomorrow->format(\DATE_RFC3339)
            ]);
    }

    /** @test */
    public function it_validates_before_or_equal()
    {
        $tomorrow = Carbon::tomorrow();

        $this->builder()
            ->rules(function ($builder) use ($tomorrow) {
                $builder->date('e1')->beforeOrEqual('2020-01-01');
                $builder->datetime('e2')->beforeOrEqual('2020-01-01');
                $builder->date('e3')->beforeOrEqual($tomorrow);
                $builder->datetime('e4')->beforeOrEqual($tomorrow);
            })
            ->assertRulesFor('e1', [
                'date_format:Y-m-d',
                'before_or_equal:2020-01-01'
            ])
            ->assertRulesFor('e2', [
                'date_format:' . DATE_RFC3339,
                'before_or_equal:2020-01-01'
            ])
            ->assertRulesFor('e3', [
                'date_format:Y-m-d',
                'before_or_equal:' . $tomorrow->format('Y-m-d')
            ])
            ->assertRulesFor('e4', [
                'date_format:' . DATE_RFC3339,
                'before_or_equal:' . $tomorrow->format(\DATE_RFC3339)
            ]);
    }

    /** @test */
    public function it_validates_the_date_format()
    {
        $this->builder()
             ->rules(function ($builder) {
                 $builder->date('e1')->format('m-d-Y');
             })
             ->assertRulesFor('e1', ['date_format:m-d-Y']);
    }

    /** @test */
    public function it_validates_the_equals_rule()
    {
        $now = Carbon::now();

        $this->builder()
             ->rules(function ($builder) use ($now) {
                 $builder->datetime('e1')->equals('2020-01-01');
                 $builder->datetime('e2')->equals($now);
             })
             ->assertRulesFor('e1', [
                 'date_format:' . DATE_RFC3339,
                 'date_equals:2020-01-01'
             ])
             ->assertRulesFor('e2', [
                 'date_format:' . DATE_RFC3339,
                 'date_equals:' . $now->format(DATE_RFC3339),
             ]);
    }
}
