<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Modify the status column to add 'booked' to the enum
            $table->enum('status', ['available', 'booked', 'rented', 'maintenance'])->default('available')->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Revert to the old enum values
            $table->enum('status', ['available', 'rented', 'maintenance'])->default('available')->change();
        });
    }
};
