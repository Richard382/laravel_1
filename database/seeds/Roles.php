<?php

use Illuminate\Database\Seeder;

class Roles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config('site.roles');

        foreach ($roles as $role) {
            \App\Role::create([
                'name' => $role['name'],
                'display_name' => $role['display_name'],
                'slug' => $role['slug']
            ]);
        }
    }
}
