<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Kinship;
use Faker\Generator as Faker;

$factory->define(Kinship::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'coefficient' => $faker->randomDigit,
    ];
});
