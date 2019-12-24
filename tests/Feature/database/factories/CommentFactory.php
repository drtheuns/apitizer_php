<?php

use Faker\Generator as Faker;
use Tests\Feature\Models\Comment;

$factory->define(Comment::class, function (Faker $faker, array $attributes) {
    return [
        'body'  => $faker->text,
    ];
});
