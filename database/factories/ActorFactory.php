<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Actor;
use App\Reference;
use App\Http\Requests\Dates;
use Faker\Generator as Faker;


$factory->define(Actor::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});

$factory->state(Actor::class, 'with_note', function (Faker $faker) {
    return [
        'note' => $faker->paragraph,
    ];
});

$factory->state(Actor::class, 'with_dates', function (Faker $faker) {
    return [
    'date_start' => $faker->date,
    'date_end' => $faker->date,
    'date_start_accuracy' => $faker->randomElement(Dates::$possibleAccuracies),
    'date_end_accuracy' => $faker->randomElement(Dates::$possibleAccuracies),
    ];
});
