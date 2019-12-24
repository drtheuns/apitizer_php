<?php

use Faker\Generator as Faker;
use Tests\Feature\Models\User;

$factory->define(User::class, function (Faker $faker, array $attributes) {
    return [
        'name'  => $faker->name,
        'email' => $faker->email,
    ];
});
