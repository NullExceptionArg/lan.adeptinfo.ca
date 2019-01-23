<?php
$factory->define(App\Model\Lan::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'lan_start' => $faker->dateTimeBetween('+6 days', '+7 days')->format('Y-m-d H:i:s'),
        'lan_end' => $faker->dateTimeBetween('+9 days', '+10 days')->format('Y-m-d H:i:s'),
        'seat_reservation_start' => $faker->dateTimeBetween('+0 days', '+3 days')->format('Y-m-d H:i:s'),
        'tournament_reservation_start' => $faker->dateTimeBetween('+4 days', '+5 days')->format('Y-m-d H:i:s'),
        "event_key" => env('EVENT_TEST_KEY'),
        "public_key" => env('SECRET_TEST_KEY'),
        "secret_key" => env('SECRET_TEST_KEY'),
        "is_current" => false,
        "latitude" => floatval($faker->latitude),
        "longitude" => floatval($faker->longitude),
        "places" => 1,
        "price" => 0,
        "rules" => $faker->text(),
        "description" => $faker->text(),
    ];
});
