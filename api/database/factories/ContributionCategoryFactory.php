<?php
$factory->define(App\Model\ContributionCategory::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name()
    ];
});
