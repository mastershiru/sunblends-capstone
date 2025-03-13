<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('orders')->insert([
            [
                'Customer_ID' => 4,
                'Order_DateTime' => Carbon::now(),
                'Order_Status' => 'Pending',
                'Payment_Method' => 'Cash',
                'Order_Type' => 'Dine-in',
                'Delivery_Method' => 'Pickup',
                'Total_Payment' => 500.00,
                'Notes' => 'No onions, please.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'Customer_ID' => 5,
                'Order_DateTime' => Carbon::now(),
                'Order_Status' => 'Delivered',
                'Payment_Method' => 'Gcash',
                'Order_Type' => 'Takeaway',
                'Delivery_Method' => 'Delivery',
                'Total_Payment' => 1200.50,
                'Notes' => 'Deliver to office address.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
