<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_acc', function (Blueprint $table) {
            $table->id('Admin_ID'); // Auto-increment primary key
            $table->string('Admin_Name')->unique();
            $table->string('Admin_Password');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_acc');
    }
};
