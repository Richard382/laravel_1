<?php

use App\Helpers\Token;
use Illuminate\Database\Seeder;

class Inquiries extends Seeder
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

//        $data = [
//            'requirements' => 'The description of requirements',
//            'price' => 'nuo 300 euru',
//            'in_hurry' => false,
//            'property_types' => [4],
//            'service_id' => 1,
//            'location' => [2, 5, 8, 1],
//            'name' => 'Algimantas Cekuolis',
//            'email' => 'algimantas@gmail.com',
//            'phone' => '+37066221319',
//            'token' => Token::generate(),
//        ];
//
//        \App\Repositories\InquiryRepository::create($data);
    }
}
