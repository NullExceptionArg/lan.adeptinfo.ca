<?php

use Seatsio\SeatsioClient;

$factory->define(App\Model\Reservation::class, function (Faker\Generator $faker) {
    $seatsClient = new SeatsioClient(env('SECRET_KEY'));
    $seatsClient->events->book(env('EVENT_TEST_KEY'), [env('SEAT_TEST_ID')]);
    return [
        "seat_id" => env('SEAT_TEST_ID')
    ];
});
