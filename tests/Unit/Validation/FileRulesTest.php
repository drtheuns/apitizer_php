<?php

namespace Tests\Unit\Validation;

use Apitizer\Validation\ObjectRules;
use Apitizer\Validation\Rules\DimensionsRule;

class FileRulesTest extends TestCase
{
    /** @test */
    public function it_generates_correct_rules()
    {
        $this->assertRules([
            't1' => ['file'],
            't2' => ['file', 'image'],
        ], function (ObjectRules $rules) {
            $rules->file('t1');
            $rules->image('t2');
        });
    }

    /** @test */
    public function it_validates_mimetypes()
    {
        $this->assertRules([
            't1' => ['file', 'mimetypes:image/jpeg'],
            't2' => ['file', 'mimes:jpeg,csv'],
        ], function (ObjectRules $rules) {
            $rules->file('t1')->mimetypes(['image/jpeg']);
            $rules->file('t2')->mimes(['jpeg', 'csv']);
        });
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

        $this->assertRules([
            't1' => [
                'file', 'image',
                'dimensions:height=100,width=200,max_height=300,max_width=400,ratio=0.5'
            ]
        ], function (ObjectRules $rules) use ($dimensions) {
            $rules->image('t1')->dimensions($dimensions);
        });
    }
}
