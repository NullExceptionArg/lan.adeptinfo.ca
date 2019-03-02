<?php
$factory->define(App\Model\Tag::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->text(5)
    ];
});
