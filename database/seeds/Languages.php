<?php

use Illuminate\Database\Seeder;

class Languages extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $langs = config('site.languages');

        foreach ($langs as $lang) {
            \App\Language::create([
                'name' => $lang
            ]);
        }
    }
}
