<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Account;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    return [
        // 'company_id' => $faker->numberBetween($min = 1, $max = 6),
        'company_id' => 1,
        'bank' => 'BDO',
        'code' => 'BDO-' . $faker->numberBetween($min = 10, $max = 99),
        'number' => $faker->creditCardNumber,
        'address' => $faker->address,
        'tel' => $faker->e164PhoneNumber,
        'email' => $faker->safeEmail,
        'contact_person' => $faker->name,
        'designation' => $faker->jobTitle,
        'fax' => $faker->e164PhoneNumber,
        'purpose' => $faker->sentence,
    ];
});
