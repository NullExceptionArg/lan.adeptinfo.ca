<?php

$factory->define(App\Model\Team::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text(20),
        'tag'  => $faker->text(5),
    ];
});
