<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(3),
        'description' => $faker->paragraph,
        'year_launched' => rand(1995, 2021),
        'opened' => rand(0, 1),
        'rating' => Video::RATING_LIST[array_rand(Video::RATING_LIST)],
        'duration' => rand(1, 60),
    ];
});
