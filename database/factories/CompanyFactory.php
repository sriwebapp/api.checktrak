<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'code' => $faker->unique()->word,
        'name' => $faker->company,
        'address' => $faker->address,
        'tel' => $faker->e164PhoneNumber,
        'tin' => $faker->unique()->numberBetween(10000000, 999999999),
        'sss' => $faker->unique()->numberBetween(10000000, 999999999),
        'hdmf' => $faker->unique()->numberBetween(10000000, 999999999),
        'phic' => $faker->unique()->numberBetween(10000000, 999999999),
    ];
});
