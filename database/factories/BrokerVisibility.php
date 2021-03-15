<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\BrokerVisibility::class, function (Faker $faker) {

    static $number = 0;

    $number++;

    return [
        'name' => sprintf('%s vieta', $number),
        'position' => $number,
        'price_per_month' => $faker->numberBetween(10, 50),
    ];
});
