<?php
$factory->define(App\Model\LanRole::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(30),
        'en_display_name' => $faker->text(50),
        'en_description' => $faker->text(500),
        'fr_display_name' => $faker->text(50),
        'fr_description' => $faker->text(500),
    ];
});