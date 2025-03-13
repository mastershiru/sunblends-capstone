<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderItemsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('order_items')->insert([
            [
                'Order_ID' => 1,
                'Customer_Email' => 'customer1@example.com',
                'Customer_Name' => 'John Doe',
                'Customer_Number' => '09123456789',
                'Item_Img' => 'uploads/item1.jpg',
                'Item_Title' => 'Burger',
                'Item_Quantity' => 2,
                'Item_Price' => 150.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'Order_ID' => 2,
                'Customer_Email' => 'customer2@example.com',
                'Customer_Name' => 'Jane Smith',
                'Customer_Number' => '09234567890',
                'Item_Img' => 'uploads/item2.jpg',
                'Item_Title' => 'Pasta',
                'Item_Quantity' => 1,
                'Item_Price' => 200.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
