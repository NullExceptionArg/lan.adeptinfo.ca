<?php
$factory->define(App\Model\Image::class, function (Faker\Generator $faker) {
    return [
        "image" => 'data:image/png;base64,' . base64_encode(base_path() . '/resources/misc/image/HammerAndSickle.png')
    ];
});
