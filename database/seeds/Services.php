<?php

use Illuminate\Database\Seeder;

class Services extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = config('site.services');

        foreach ($services as $type_user => $params) {
            \App\Service::create([
                'type_user' => $type_user,
                'type_broker' => $params['broker'],
                'type' => $params['type']
            ]);
        }
    }
}
