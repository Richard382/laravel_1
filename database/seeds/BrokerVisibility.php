<?php

use Illuminate\Database\Seeder;

class BrokerVisibility extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\BrokerVisibility::class, 10)->create();
    }
}
