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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g. 'order.status_changed'
            $table->unsignedBigInteger('customer_id')->nullable(); // Who should see this notification
            $table->unsignedBigInteger('order_id')->nullable(); // Related order
            $table->string('status')->nullable(); // Order status
            $table->string('message'); // Display message
            $table->text('data')->nullable(); // JSON data for the notification
            $table->timestamp('read_at')->nullable(); // When it was read
            $table->timestamps();
            
            // Indexes for faster lookups
            $table->index('customer_id');
            $table->index('read_at');

            // Foreign keys
            $table->foreign('customer_id')->references('customer_id')->on('customer')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('order_id')->references('order_id')->on('order_detail')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
