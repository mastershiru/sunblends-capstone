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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id('rating_id');
            $table->unsignedBigInteger('dish_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('order_id');
            $table->integer('rating')->default(0); // 0-5 stars
            $table->text('review')->nullable(); // Optional review text
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('dish_id')->references('dish_id')->on('dish')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('customer')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('order_id')->references('order_id')->on('order_detail')->onDelete('cascade')->onUpdate('cascade');
            
            // Ensure a customer can only rate a specific dish from a specific order once
            $table->unique(['customer_id', 'dish_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
