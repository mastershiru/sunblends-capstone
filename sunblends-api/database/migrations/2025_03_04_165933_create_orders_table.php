<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('Order_ID'); // Auto-incrementing primary key
            $table->unsignedBigInteger('Customer_ID'); // Foreign key reference
            $table->dateTime('Order_DateTime'); // Order timestamp
            $table->enum('Order_Status', ['Pending', 'Delivered', 'Cancelled'])->default('Pending'); // Status
            $table->enum('Payment_Method', ['Cash', 'Gcash']); // Payment method
            $table->string('Order_Type'); // Order type
            $table->enum('Delivery_Method', ['Pickup', 'Delivery']); // Pickup or Delivery
            $table->decimal('Total_Payment', 10, 2); // Payment total
            $table->text('Notes')->nullable(); // Optional notes
            $table->timestamps(); // Laravel timestamps

            // Foreign key constraint
            $table->foreign('Customer_ID')->references('Customer_ID')->on('customer')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
