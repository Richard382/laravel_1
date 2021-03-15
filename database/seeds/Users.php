<?php

use Illuminate\Database\Seeder;

class Users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ( ! env('TEST_DATA')) {
            return;
        }

        factory(\App\User::class, 10)->create();
        factory(\App\User::class, 10)->state('broker')->create();

//        $broker = [
//            'name'              => 'Test User',
//            'agency'            => 'Test Agiency',
//            'paths'             => [
//                0 => [
//                    "property_type" => 4,
//                    "properties" => 31,
//                    "regions" => [2,5],
//                    "price_from" => "100",
//                    "price_to" => "800",
//                    "services" => [2,3,4,5]
//                ],
//            ],
//            'language'          => [1, 2, 3],
//            'description'       => 'Some typical description',
//            'phone'             => '+37066221319',
//            'email'             => 'test@gmail.com',
//            'represent_video'   => 'http://yourube.video',
//            'password'          => 'sd123456',
//        ];
//
//        \App\Repositories\BrokerRepository::create($broker);
    }
}
