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
            ], // Adding images from Shoot folder
            ['dish_name' => 'Bacsilog', 'dish_picture' => 'images/dish/Bacsilog.png', 'category' => 'Filipino', 'dish_available' => '1', 'dish_rating' => '4.7', 'Price' => 149.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'BLT Sandwich', 'dish_picture' => 'images/dish/blt.png', 'category' => 'Sandwich', 'dish_available' => '1', 'dish_rating' => '4.5', 'Price' => 129.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'Brownies', 'dish_picture' => 'images/dish/brownies.png', 'category' => 'Dessert', 'dish_available' => '1', 'dish_rating' => '4.8', 'Price' => 79.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'Lemonade', 'dish_picture' => 'images/dish/Lemonade.png', 'category' => 'Beverage', 'dish_available' => '1', 'dish_rating' => '4.6', 'Price' => 59.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'Matcha Latte', 'dish_picture' => 'images/dish/matchalatte.png', 'category' => 'Beverage', 'dish_available' => '1', 'dish_rating' => '4.9', 'Price' => 89.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'Mozzarella Bombs', 'dish_picture' => 'images/dish/mozzarellabombs.png', 'category' => 'Appetizer', 'dish_available' => '1', 'dish_rating' => '4.7', 'Price' => 99.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'Oatmeal Cookie', 'dish_picture' => 'images/dish/Oatmealcookie.png', 'category' => 'Dessert', 'dish_available' => '1', 'dish_rating' => '4.5', 'Price' => 69.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'Pizza', 'dish_picture' => 'images/dish/pizza.png', 'category' => 'Pizza', 'dish_available' => '1', 'dish_rating' => '4.8', 'Price' => 199.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'Red Velvet Cookie', 'dish_picture' => 'images/dish/Redvelvetcookie.png', 'category' => 'Dessert', 'dish_available' => '1', 'dish_rating' => '4.6', 'Price' => 79.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'Revel Bar', 'dish_picture' => 'images/dish/Revelbar.png', 'category' => 'Dessert', 'dish_available' => '1', 'dish_rating' => '4.7', 'Price' => 89.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['dish_name' => 'Strawberry Shake', 'dish_picture' => 'images/dish/Strawberryshake.png', 'category' => 'Beverage', 'dish_available' => '1', 'dish_rating' => '4.9', 'Price' => 99.99, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        Dish::insert($dishes);
    }
}
