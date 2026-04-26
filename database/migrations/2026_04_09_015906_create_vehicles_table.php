<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('vehicle_ID');
            $table->string('brand');
            $table->string('model');
            $table->string('color');
            $table->string('plate_no')->unique();
            $table->string('category');
            $table->decimal('daily_rate', 10, 2);
            $table->string('image')->nullable();
            $table->enum('status', ['available', 'booked', 'rented', 'maintenance'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};