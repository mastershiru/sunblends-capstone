<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run()
    {
        DB::table('menu')->insert([
            [
                'Dish_ID' => 25,
                'Dish_Img' => 'uploads\\9dcaf70135cfccffc4ed8a6bc8cc80a5',
                'Dish_Title' => 'Fish & Chips',
                'Dish_Type' => 'Fish',
                'Dish_Persons' => 1,
                'Dish_Price' => 359,
                'isAvailable' => 1,
                'Dish_Rating' => 4.30,
            ],
            [
                'Dish_ID' => 26,
                'Dish_Img' => 'uploads\\065d57dc4dadbab2f0371f53a52ae679',
                'Dish_Title' => 'Spaghetti w/ Feta Cheese',
                'Dish_Type' => 'Pasta',
                'Dish_Persons' => 1,
                'Dish_Price' => 399,
                'isAvailable' => 1,
                'Dish_Rating' => 4.00,
            ],
            [
                'Dish_ID' => 27,
                'Dish_Img' => 'uploads\\a6a130946bdcf3e4093084bf09b4c874',
                'Dish_Title' => 'Ramen',
                'Dish_Type' => 'Noodles',
                'Dish_Persons' => 1,
                'Dish_Price' => 379,
                'isAvailable' => 1,
                'Dish_Rating' => 4.50,
            ],
            [
                'Dish_ID' => 28,
                'Dish_Img' => 'uploads\\bf993486646642c850fa71fcf1fc3c95',
                'Dish_Title' => 'Ground Beef Kebabs',
                'Dish_Type' => 'Meat',
                'Dish_Persons' => 1,
                'Dish_Price' => 99,
                'isAvailable' => 1,
                'Dish_Rating' => 5.00,
            ],
            [
                'Dish_ID' => 49,
                'Dish_Img' => 'uploads\\55f51082bb0ad7844cf2f86dd419065d',
                'Dish_Title' => 'Breakfast egg & bacon',
                'Dish_Type' => 'Non Veg',
                'Dish_Persons' => 2,
                'Dish_Price' => 99,
                'isAvailable' => 0,
                'Dish_Rating' => 4.30,
            ],
            [
                'Dish_ID' => 50,
                'Dish_Img' => 'uploads\\b7ec817a27f8db5a53bd92723dc15d0e',
                'Dish_Title' => 'Tomato Basil Penne Pasta',
                'Dish_Type' => 'Pasta',
                'Dish_Persons' => 1,
                'Dish_Price' => 159,
                'isAvailable' => 1,
                'Dish_Rating' => 4.30,
            ],
        ]);
    }
}
