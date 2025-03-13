<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id('Dish_ID'); // Auto-increment primary key
            $table->string('Dish_Img'); // Path to the image file
            $table->string('Dish_Title'); // Name of the dish
            $table->string('Dish_Type'); // Category (e.g., Pasta, Meat, etc.)
            $table->integer('Dish_Persons'); // Servings per dish
            $table->decimal('Dish_Price', 8, 2); // Price with decimal
            $table->boolean('isAvailable')->default(true); // Availability status
            $table->float('Dish_Rating')->default(0); // Rating
            $table->timestamps(); // Created_at & Updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu');
    }
};
