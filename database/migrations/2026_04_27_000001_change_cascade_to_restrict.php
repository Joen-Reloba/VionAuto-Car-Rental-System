<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change customers.user_ID foreign key from cascade to restrict
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_ID']);
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('restrict');
        });

        // Change staffs.user_ID foreign key from cascade to restrict
        Schema::table('staffs', function (Blueprint $table) {
            $table->dropForeign(['user_ID']);
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('restrict');
        });

        // Change bookings.approved_by_user_id foreign key from cascade to restrict
        Schema::table('bookings', function (Blueprint $table) {
            DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_approved_by_user_id_foreign');
            $table->foreign('approved_by_user_id')->references('user_ID')->on('staffs')->onDelete('restrict');
        });

        // Change bookings.customer_user_id foreign key from cascade to restrict
        Schema::table('bookings', function (Blueprint $table) {
            DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_customer_user_id_foreign');
            $table->foreign('customer_user_id')->references('user_ID')->on('customers')->onDelete('restrict');
        });

        // Change bookings.vehicle_ID foreign key from cascade to restrict
        Schema::table('bookings', function (Blueprint $table) {
            DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_vehicle_id_foreign');
            $table->foreign('vehicle_ID')->references('vehicle_ID')->on('vehicles')->onDelete('restrict');
        });

        // Change payments.booking_ID foreign key from cascade to restrict
        Schema::table('payments', function (Blueprint $table) {
            DB::statement('ALTER TABLE payments DROP FOREIGN KEY payment_booking_id_foreign');
            $table->foreign('booking_ID')->references('booking_ID')->on('bookings')->onDelete('restrict');
        });

        // Change payments.verified_by_user_id foreign key from cascade to restrict
        Schema::table('payments', function (Blueprint $table) {
            DB::statement('ALTER TABLE payments DROP FOREIGN KEY payments_verified_by_user_id_foreign');
            $table->foreign('verified_by_user_id')->references('user_ID')->on('staffs')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        // Revert to cascade
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_ID']);
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('cascade');
        });

        Schema::table('staffs', function (Blueprint $table) {
            $table->dropForeign(['user_ID']);
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('cascade');
        });

        Schema::table('bookings', function (Blueprint $table) {
            DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_approved_by_user_id_foreign');
            $table->foreign('approved_by_user_id')->references('user_ID')->on('staffs')->onDelete('cascade');
        });

        Schema::table('bookings', function (Blueprint $table) {
            DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_customer_user_id_foreign');
            $table->foreign('customer_user_id')->references('user_ID')->on('customers')->onDelete('cascade');
        });

        Schema::table('bookings', function (Blueprint $table) {
            DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_vehicle_id_foreign');
            $table->foreign('vehicle_ID')->references('vehicle_ID')->on('vehicles')->onDelete('cascade');
        });

        Schema::table('payments', function (Blueprint $table) {
            DB::statement('ALTER TABLE payments DROP FOREIGN KEY payment_booking_id_foreign');
            $table->foreign('booking_ID')->references('booking_ID')->on('bookings')->onDelete('cascade');
        });

        Schema::table('payments', function (Blueprint $table) {
            DB::statement('ALTER TABLE payments DROP FOREIGN KEY payments_verified_by_user_id_foreign');
            $table->foreign('verified_by_user_id')->references('user_ID')->on('staffs')->onDelete('cascade');
        });
    }
};
