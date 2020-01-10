<?php

use Faker\Generator as Faker;
use Tests\Feature\Models\Tag;

$factory->define(Tag::class, function (Faker $faker, array $attributes) {
    return [
        'name'   => $faker->name,
        'weight' => $faker->randomFloat(2, 0, 1),
    ];
});
