<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->string('Customer_Number')->nullable()->change(); // Make it nullable
        });
    }
    
    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->string('Customer_Number')->nullable(false)->change(); // Restore to non-nullable
        });
    }
    
};
