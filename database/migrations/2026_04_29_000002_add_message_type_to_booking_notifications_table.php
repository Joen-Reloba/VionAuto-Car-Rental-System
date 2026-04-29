<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE booking_notifications MODIFY type ENUM('approved', 'rejected', 'pending', 'rental_started', 'vehicle_returned', 'payment_approved', 'payment_rejected', 'message') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE booking_notifications MODIFY type ENUM('approved', 'rejected', 'pending', 'rental_started', 'vehicle_returned', 'payment_approved', 'payment_rejected') DEFAULT 'pending'");
    }
};
