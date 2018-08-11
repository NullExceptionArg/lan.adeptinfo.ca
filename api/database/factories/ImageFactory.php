<?php
$factory->define(App\Model\Image::class, function (Faker\Generator $faker) {
    $image = $faker->image();
    if ($image) {
        return [
            "image" => 'data:image/png;base64,' . base64_encode(file_get_contents($image))
        ];
    } else {
        return [
            "image" => 'data:image/png;base64,' . base64_encode(base_path() . '/resources/misc/image/HammerAndSickle.png')
        ];
    }
});