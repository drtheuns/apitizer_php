<?php

use Faker\Generator as Faker;
use Tests\Feature\Models\User;
use Tests\Feature\Models\Post;

$factory->define(User::class, function (Faker $faker, array $attributes) {
    return [
        'name'  => $faker->name,
        'email' => $faker->email,
        'active' => 1,
    ];
});

$factory->state(User::class, 'withPosts', []);
$factory->afterCreatingState(User::class, 'withPosts', function ($post) {
    $post->comments()->saveMany(
        factory(Post::class, 2)->make(['author_id' => $post->author_id])->all()
    );
});
