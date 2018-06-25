<?php
$factory->define(App\Model\Lan::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'lan_start' => $faker->dateTimeBetween('+0 days', '+3 days')->format('Y-m-d H:i:s'),
        'lan_end' => $faker->dateTimeBetween('+0 days', '+3 days')->format('Y-m-d H:i:s'),
        'seat_reservation_start' => $faker->dateTimeBetween('+4 days', '+5 days')->format('Y-m-d H:i:s'),
        'tournament_reservation_start' => $faker->dateTimeBetween('+6 days', '+7 days')->format('Y-m-d H:i:s'),
        "event_key_id" => env('EVENT_KEY_ID'),
        "public_key_id" => env('PUBLIC_KEY_ID'),
        "secret_key_id" => env('SECRET_KEY_ID'),
        "latitude" => $faker->latitude,
        "longitude" => $faker->longitude,
        "places" => 1,
        "price" => 0,
        "rules" => $faker->text(),
        "description" => $faker->text()
    ];
});