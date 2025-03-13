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
        Schema::create('transaction', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->string('transaction_reference')->unique();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            
            
            $table->decimal('cash_amount', 10, 2);
            $table->string('change_amount')->nullable();
            $table->string('transaction_status')->default('pending');
            $table->timestamp('transaction_date');
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('order_detail')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('customer')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
