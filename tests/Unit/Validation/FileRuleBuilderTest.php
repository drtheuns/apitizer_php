<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\Rules\DimensionsRule;

class FileRuleBuilderTest extends TestCase
{
    /** @test */
    public function it_validates_mimetypes()
    {
        $this->builder()
             ->rules(function ($builder) {
                 $builder->file('e1')->mimetypes(['image/jpeg']);
                 $builder->file('e2')->mimes(['jpeg', 'csv']);
             })
            ->assertRules([
                'e1' => ['file', 'mimetypes:image/jpeg'],
                'e2' => ['file', 'mimes:jpeg,csv'],
            ]);
    }

    /** @test */
    public function it_validates_images()
    {
        $this->assertSimpleRule('file', 'image');
    }

    /** @test */
    public function it_validates_dimensions()
    {
        $dimensions = (new DimensionsRule())
                    ->height(100)
                    ->width(200)
                    ->maxHeight(300)
                    ->maxWidth(400)
                    ->ratio(1 / 2);

        $this->builder()
             ->rules(function ($builder) use ($dimensions) {
                 $builder->image('e1')->dimensions($dimensions);
             })
             ->assertRulesFor('e1', [
                 'file', 'image',
                 'dimensions:height=100,width=200,max_height=300,max_width=400,ratio=0.5'
             ]);
    }
}
