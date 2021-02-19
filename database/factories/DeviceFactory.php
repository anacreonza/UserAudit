<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Device;
use Faker\Generator as Faker;

$factory->define(Device::class, function (Faker $faker) {
    return [
        'username' => $faker->firstname . $faker->lastname,
        'computername' => $faker->numerify('02CORM-########'),
        'reportjson' => $faker->paragraph()
    ];
});
