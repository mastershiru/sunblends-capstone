<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->id('Customer_ID');
            $table->string('Customer_Name');
            $table->string('Customer_Email')->unique();
            $table->string('Customer_Number');
            $table->string('Customer_Password');
            $table->string('Customer_Img')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer');
    }
}
