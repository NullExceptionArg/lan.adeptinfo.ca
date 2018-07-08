<?php

use Seatsio\SeatsioClient;

$factory->define(App\Model\Reservation::class, function (Faker\Generator $faker) {
    $seatsClient = new SeatsioClient(env('SECRET_KEY'));
    $seatsClient->events()->book(env('EVENT_KEY'), [env('SEAT_ID')]);
    return [
        "seat_id" => env('SEAT_ID')
    ];
});