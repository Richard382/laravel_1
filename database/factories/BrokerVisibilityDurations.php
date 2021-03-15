<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\BrokerVisibilityDurations::class, function (Faker $faker) {

    static $number = 0;

    $number++;

    return [
        'name' => sprintf('%s %s', $number, $number === 1 ? 'mėnesis' : 'mėnesiai'),
        'months' => $number
    ];
});
