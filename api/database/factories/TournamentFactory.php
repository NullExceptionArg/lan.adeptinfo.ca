<?php
$factory->define(App\Model\Tournament::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'players_to_reach' => rand(1, 20),
        'teams_to_reach' => rand(1, 20),
        'rules' => $faker->text,
        'price' => rand(0, 2000),
    ];
});
