<?php

use Faker\Generator as Faker;
use Tests\Feature\Models\Post;

$factory->define(Post::class, function (Faker $faker, array $attributes) {
    return [
        'title'  => $faker->catchPhrase,
        'body' => $faker->text,
    ];
});
