<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id('staff_ID');
            $table->unsignedBigInteger('user_ID');
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('cascade');
            $table->string('employee_no')->unique();
            $table->string('position')->nullable();
            $table->date('hired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};