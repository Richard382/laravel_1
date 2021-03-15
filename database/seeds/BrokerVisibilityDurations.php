<?php

use Illuminate\Database\Seeder;

class BrokerVisibilityDurations extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\BrokerVisibilityDurations::class, 12)->create();
    }
}
