<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('Item_ID'); // Auto-incrementing primary key
            $table->unsignedBigInteger('Order_ID')->nullable();
            $table->string('Customer_Email')->nullable();
            $table->string('Customer_Name')->nullable();
            $table->string('Customer_Number', 50);
            $table->string('Item_Img')->nullable();
            $table->string('Item_Title')->nullable();
            $table->integer('Item_Quantity')->nullable();
            $table->decimal('Item_Price', 10, 2)->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('Order_ID')->references('Order_ID')->on('orders')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
