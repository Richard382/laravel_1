<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Language;
use App\Model;
use App\Property;
use App\PropertyType;
use App\Region;
use App\Service;
use Faker\Generator as Faker;

$factory->define(\App\Company::class, function (Faker $faker) {



    return [
        'name' => $this->faker->company,
        'description' => $this->faker->realText(255),
        'website' => $this->faker->url,
        'represent_video' => $this->faker->url,
    ];
});

// Assign languages
$factory->afterCreating(\App\Company::class, function($company, Faker $faker) {
    $languages = Language::all();

    $company->languages()->sync($languages->random($faker->numberBetween(1, $languages->count()))->pluck('id')->toArray());
});

// Assign paths
$factory->afterCreating(\App\Company::class, function($company, Faker $faker) {
    $regions = Region::all();
    $services = Service::all();
    $property_types = PropertyType::all();

    for ($i = 0; $i < $faker->numberBetween(1, 10); $i++) {
        $property_type = $property_types->random();
        $price_from = $faker->numberBetween(1, 100000);

        $sphere = $company
            ->spheres()
            ->create([
                'price_from' => $price_from,
                'price_to' => $faker->numberBetween($price_from, 200000)
            ]);

        $sphere
            ->type()
            ->associate($property_type);

        // Sync Sphere properties
        $sphere
            ->property()
            ->associate($property_type->properties->random());

        // Save associates
        $sphere
            ->save();

        // Sync Sphere cities
        $sphere
            ->regions()
            ->sync($regions->random($faker->numberBetween(1, $regions->count()))->pluck('id')->toArray());

        $sphere
            ->services()
            ->sync($services->random($faker->numberBetween(1, $services->count()))->pluck('id')->toArray());
    }
});
