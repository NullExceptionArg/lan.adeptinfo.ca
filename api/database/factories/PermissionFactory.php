<?php

$factory->define(App\Model\Permission::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'can_be_per_lan' => false,
    ];
});
