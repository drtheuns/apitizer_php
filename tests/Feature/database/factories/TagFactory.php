<?php

use Faker\Generator as Faker;
use Tests\Feature\Models\Tag;

$factory->define(Tag::class, function (Faker $faker, array $attributes) {
    return [
        'name' => $faker->name,
    ];
});
