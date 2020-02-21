<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\RuleBuilder;

class StringRuleBuilderTest extends TestCase
{
    /** @test */
    public function it_always_validates_the_type()
    {
        $this->builder()
             ->rules(function (RuleBuilder $builder) {
                 $builder->string('name');
             })
             ->assertRulesFor('name', ['string']);
    }

    /** @test */
    public function it_validates_simple_rules()
    {
        $this->simpleRule('activeUrl');
        $this->simpleRule('alpha');
        $this->simpleRule('alphaDash');
        $this->simpleRule('alphaNum');
        $this->simpleRule('ip');
        $this->simpleRule('ipv4');
        $this->simpleRule('ipv6');
        $this->simpleRule('json');
        $this->simpleRule('numeric');
        $this->simpleRule('timezone');
        $this->simpleRule('url');
        $this->simpleRule('uuid');
    }

    /** @test */
    public function it_validates_digits()
    {
        $this->builder()
             ->rules(function ($builder) {
                 $builder->string('name')->digits(5);
             })
             ->assertRulesFor('name', ['string', 'digits:5']);
    }

    /** @test */
    public function it_validates_digits_between()
    {
        $this->builder()
             ->rules(function ($builder) {
                 $builder->string('name')->digitsBetween(4, 12);
             })
             ->assertRulesFor('name', ['string', 'digits_between:4,12']);
    }

    /** @test */
    public function it_validates_emails()
    {
        $this->builder()
             ->rules(function ($builder) {
                 $builder->string('e1')->email();
                 $builder->string('e2')->email(['spoof', 'filter', 'dns']);
             })
             ->assertRulesFor('e1', ['string', 'email:rfc'])
             ->assertRulesFor('e2', ['string', 'email:spoof,filter,dns']);
    }

    /** @test */
    public function it_validates_ends_with()
    {
        $this->builder()
             ->rules(function ($builder) {
                 $builder->string('e1')->endsWith('name');
                 $builder->string('e2')->endsWith(['name', 'wow']);
             })
             ->assertRulesFor('e1', ['string', 'ends_with:name'])
             ->assertRulesFor('e2', ['string', 'ends_with:name,wow']);
    }

    /** @test */
    public function it_validates_starts_with()
    {
        $this->builder()
            ->rules(function ($builder) {
                $builder->string('e1')->startsWith('name');
                $builder->string('e2')->startsWith(['name', 'wow']);
            })
            ->assertRulesFor('e1', ['string', 'starts_with:name'])
            ->assertRulesFor('e2', ['string', 'starts_with:name,wow']);
    }

    public function simpleRule(string $rule)
    {
        parent::assertSimpleRule('string', $rule);
    }
}
