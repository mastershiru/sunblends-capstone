<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('admin_acc')->insert([
            'Admin_Name' => 'admin',
            'Admin_Password' => Hash::make('password123'), // Change to a secure password
        ]);
    }
}
