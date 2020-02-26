<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\RuleBuilder;
use Apitizer\Validation\Rules\RequiredIfRule;

class TypedRuleBuilderTest extends TestCase
{
    /** @test */
    public function it_validates_confirmed_fields()
    {
        $this->assertSimpleRule('string', 'confirmed');
    }

    /** @test */
    public function it_bails_validation()
    {
        $this->assertSimpleRule('string', 'bail');
    }

    /** @test */
    public function it_validates_the_between_rule()
    {
        $this->builder()
             ->rules(function ($builder) {
                 $builder->string('e1')->between(50, 40);
             })
             ->assertRulesFor('e1', ['string', 'between:50,40']);
    }

    /** @test */
    public function it_validates_the_field_is_different_or_same()
    {
        $this->builder()
             ->rules(function ($builder) {
                 $builder->string('e1')->different('e2');
                 $builder->string('e2')->same('e1');
             })
             ->assertRulesFor('e1', ['string', 'different:e2'])
             ->assertRulesFor('e2', ['string', 'same:e1']);
    }

    /** @test */
    public function it_validates_comparison_rules()
    {
        $this->builder()
             ->rules(function (RuleBuilder $builder) {
                 $builder->integer('e1')->min(5);
                 $builder->integer('e2')->max(5);
                 $builder->integer('e3')->gt(5);
                 $builder->integer('e4')->gte(5);
                 $builder->integer('e5')->lt(5);
                 $builder->integer('e6')->lte(5);
             })
             ->assertRules([
                 'e1' => ['integer', 'min:5'],
                 'e2' => ['integer', 'max:5'],
                 'e3' => ['integer', 'gt:5'],
                 'e4' => ['integer', 'gte:5'],
                 'e5' => ['integer', 'lt:5'],
                 'e6' => ['integer', 'lte:5'],
             ]);
    }

    /** @test */
    public function it_validates_regex()
    {
        $this->builder()
             ->rules(function (RuleBuilder $builder) {
                 $builder->string('e1')->regex('[a-z]+');
                 $builder->string('e2')->notRegex('[a-z]+');
             })
            ->assertRules([
                'e1' => ['string', 'regex:/[a-z]+/'],
                'e2' => ['string', 'not_regex:/[a-z]+/'],
            ]);
    }

    /** @test */
    public function it_validates_the_required_rules(): void
    {
        $this->builder()
             ->rules(function (RuleBuilder $builder) {
                 $builder->string('e1')->required();
                 $builder->string('e2')->requiredWith(['e1']);
                 $builder->string('e3')->requiredWithAll(['e2']);
                 $builder->string('e4')->requiredWithout(['e3']);
                 $builder->string('e5')->requiredWithoutAll(['e4']);
                 $builder->string('e6')->requiredUnless('e5', 'wow');
             })
             ->assertRules([
                 'e1' => ['string', 'required'],
                 'e2' => ['string', 'required_with:e1'],
                 'e3' => ['string', 'required_with_all:e2'],
                 'e4' => ['string', 'required_without:e3'],
                 'e5' => ['string', 'required_without_all:e4'],
                 'e6' => ['string', 'required_unless:e5,wow'],
             ]);
    }

    /** @test */
    public function it_validates_nullables(): void
    {
        $this->builder()
             ->rules(function (RuleBuilder $builder) {
                 $builder->string('e1')->nullable();
                 $builder->string('e2')->nullable()->required();
             })
             ->assertRules([
                 'e1' => ['string', 'nullable'],
                 'e2' => ['string', 'required', 'nullable'],
             ]);
    }
}
