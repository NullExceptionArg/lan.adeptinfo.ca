<?php

$factory->define(App\Model\User::class, function (Faker\Generator $faker) {
    return [
        'first_name'=> $faker->firstName,
        'last_name' => $faker->lastName,
        'email'     => $faker->unique()->email,
        'password'  => \Illuminate\Support\Facades\Hash::make('Passw0rd!'),
    ];
});
