<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        DB::table('customer')->insert([
            [
                'Customer_Name' => 'John Doe',
                'Customer_Email' => 'johndoe@example.com',
                'Customer_Number' => '09171234567',
                'Customer_Password' => bcrypt('password123'), // Make sure to hash the password
                'Customer_Img' => 'john_doe.jpg',
            ]
        ]);
    }
}
