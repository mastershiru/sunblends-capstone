<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Dish;
use Carbon\Carbon;


class dishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dishes = [
            [
                'dish_name' => 'Bacon and eggs',
                'dish_picture' => 'images/dish/dish1.png',
                'category' => 'Breakfast',
                'dish_available' => '1',
                'dish_rating' => '4.8',
                'Price' => 159.99,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dish_name' => 'Fish and Chips',
                'dish_picture' => 'images/dish/dish2.png',
                'category' => 'Seafood',
                'dish_available' => '1',
                'dish_rating' => '4.5',
                'Price' => 119.50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dish_name' => 'Chicken Pasta Alfredo',
                'dish_picture' => 'images/dish/dish3.png',
                'category' => 'Pasta',
                'dish_available' => '1',
                'dish_rating' => '4.7',
                'Price' => 189.99,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dish_name' => 'Ramen Seafood',
                'dish_picture' => 'images/dish/dish4.png',
                'category' => 'Ramen',
                'dish_available' => '1',
                'dish_rating' => '4.6',
                'Price' => 129.50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dish_name' => 'Grilled Steak',
                'dish_picture' => 'images/dish/dish5.png',
                'category' => 'Steak',
                'dish_available' => '1',
                'dish_rating' => '4.9',
                'Price' => 249.99,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dish_name' => 'Carbonara Pasta',
                'dish_picture' => 'images/dish/dish6.png',
                'category' => 'Pasta',
                'dish_available' => '1',
                'dish_rating' => '4.9',
                'Price' => 99.50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        Dish::insert($dishes);
    }
}
