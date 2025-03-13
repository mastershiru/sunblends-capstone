<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dish_items', function (Blueprint $table) {
            $table->id('dish_item_id');
            $table->unsignedBigInteger('dish_id');
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('inventory_id')->nullable();
            
            $table->foreign('dish_id')->references('dish_id')->on('dish')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('cart_id')->references('cart_id')->on('cart')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('order_id')->references('order_id')->on('order_detail')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dish_items');
    }
};
