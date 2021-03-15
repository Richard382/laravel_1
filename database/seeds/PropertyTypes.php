<?php

use Illuminate\Database\Seeder;

class PropertyTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types_with_objects = config('site.taxos');

        foreach ($types_with_objects as $key => $objects) {
            $type = \App\PropertyType::create([
                'name' => $key
            ]);

            foreach ($objects as $object) {
                $type->properties()->create([
                    'name' => $object
                ]);
            }
        }
    }
}
