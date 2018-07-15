<?php

use Faker\Generator as Faker;

$factory->define(App\Corps::class, function (Faker $faker) {
    return [
            'corporation_name' => $faker->company,
            'registration_num' => '4440121000' . (string)($faker->randomNumber($nbDigits = 6, $strict = false)),
            'address' => $faker->address,
            'represent_person' => $faker->name,
            'phone' => $faker->phoneNumber,
            'contact_person' => $faker->name,
            'contact_phone' => $faker->phoneNumber,
            'inspection_status' => $faker->text($maxNbChars = 200),
            'phone_call_record' => $faker->text($maxNbChars = 200),
            'corporation_aic_division' => 'SL'
    ];
});
