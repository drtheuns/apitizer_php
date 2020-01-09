<?php

use Faker\Generator as Faker;
use Tests\Feature\Models\User;
use Tests\Feature\Models\Post;
use Tests\Feature\Models\Tag;

$factory->define(Post::class, function (Faker $faker, array $attributes) {
    return [
        'title'  => $faker->catchPhrase,
        'body' => $faker->text,
        'author_id' => $attributes['author_id'] ?? function () {
            return factory(User::class)->create()->id;
        }
    ];
});

$factory->state(Post::class, 'withTags', []);
$factory->afterCreatingState(Post::class, 'withTags', function ($post, $faker) {
    $post->tags()->saveMany(factory(Tag::class, 2)->make()->all());
});
