<?php

use Illuminate\Database\Seeder;

class Cities extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = config('regions');

        foreach ($regions as $name => $options) {
            $region = \App\Region::create([
                'name' => $name,
                'order' => isset($options['order']) ? $options['order'] : NULL
            ]);

            if (! isset($options['items'])) {
                continue;
            }

            foreach ($options['items'] as $city) {
                $city = \App\City::create([
                    'name' => $city,
                    'order' => isset($options['order']) ? $options['order'] : NULL
                ]);

                $city
                    ->region()
                    ->associate($region);

                $city->save();
            }
        }
    }
}
