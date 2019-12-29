<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Reference;
use Faker\Generator as Faker;

$factory->define(Reference::class, function (Faker $faker) {
    $categories = ['source', 'bibliography'];
    return [
        'category' => $faker->randomElement($categories),
        'name' => $faker->name,
    ];
});

$factory->state(Reference::class, 'with_note', function (Faker $faker) {
    return [
        'note' => $faker->paragraph,
    ];
});
