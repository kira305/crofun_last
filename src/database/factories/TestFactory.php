<?php

use Faker\Generator as Faker;

$factory->define(App\TestSeeder::class, function (Faker $faker) {
    return [
        
           	'file_id'         => $faker->randomNumber(),
           	'err'             => $faker->sentence(),
           	'err_row'         => $faker->randomNumber(),
    ];
});
