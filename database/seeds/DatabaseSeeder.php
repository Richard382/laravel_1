<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            Cities::class,
            PropertyTypes::class,
            Languages::class,
            Services::class,
            Roles::class,
            Users::class,
            Companies::class,
            Inquiries::class,
            BrokerVisibility::class,
            BrokerVisibilityDurations::class
        ]);
    }
}
