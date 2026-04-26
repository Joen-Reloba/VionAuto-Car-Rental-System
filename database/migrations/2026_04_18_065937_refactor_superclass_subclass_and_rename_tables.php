<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── STEP 1: Drop all affected foreign keys first ──────────────────

        Schema::table('bookings', function (Blueprint $table) {
            try { $table->dropForeign('bookings_customer_id_foreign'); } catch (\Exception $e) {}
            try { $table->dropForeign('bookings_approved_by_foreign'); } catch (\Exception $e) {}
        });

        Schema::table('payment', function (Blueprint $table) {
            try { $table->dropForeign('payment_verified_by_foreign'); } catch (\Exception $e) {}
        });

        // ── STEP 2: Restructure customers (user_ID becomes PK) ────────────

        Schema::table('customers', function (Blueprint $table) {
            try { $table->dropForeign('customers_user_id_foreign'); } catch (\Exception $e) {}
        });

        DB::statement('ALTER TABLE customers MODIFY customer_ID BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE customers DROP PRIMARY KEY');
        DB::statement('ALTER TABLE customers DROP COLUMN customer_ID');
        DB::statement('ALTER TABLE customers ADD PRIMARY KEY (user_ID)');

        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('cascade');
        });

        // ── STEP 3: Restructure staff → staffs (user_ID becomes PK) ──────

        Schema::table('staff', function (Blueprint $table) {
            try { $table->dropForeign('staff_user_id_foreign'); } catch (\Exception $e) {}
        });

        DB::statement('ALTER TABLE staff MODIFY staff_ID BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE staff DROP PRIMARY KEY');
        DB::statement('ALTER TABLE staff DROP COLUMN staff_ID');
        DB::statement('ALTER TABLE staff ADD PRIMARY KEY (user_ID)');

        // ── STEP 4: Rename tables ─────────────────────────────────────────

        Schema::rename('staff', 'staffs');
        Schema::rename('payment', 'payments');

        Schema::table('staffs', function (Blueprint $table) {
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('cascade');
        });

        // ── STEP 5: Fix bookings columns ──────────────────────────────────

        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('customer_ID', 'customer_user_id');
            $table->renameColumn('approved_by', 'approved_by_user_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('customer_user_id')
                  ->references('user_ID')->on('customers')
                  ->onDelete('cascade');

            $table->foreign('approved_by_user_id')
                  ->references('user_ID')->on('staffs')
                  ->onDelete('set null');
        });

        // ── STEP 6: Fix payments.verified_by ─────────────────────────────

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('verified_by', 'verified_by_user_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('verified_by_user_id')
                  ->references('user_ID')->on('staffs')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // ── Drop new foreign keys ─────────────────────────────────────────

        Schema::table('payments', function (Blueprint $table) {
            try { $table->dropForeign(['verified_by_user_id']); } catch (\Exception $e) {}
            $table->renameColumn('verified_by_user_id', 'verified_by');
        });

        Schema::table('bookings', function (Blueprint $table) {
            try { $table->dropForeign(['customer_user_id']); } catch (\Exception $e) {}
            try { $table->dropForeign(['approved_by_user_id']); } catch (\Exception $e) {}
            $table->renameColumn('customer_user_id', 'customer_ID');
            $table->renameColumn('approved_by_user_id', 'approved_by');
        });

        // ── Rename tables back ────────────────────────────────────────────

        Schema::rename('payments', 'payment');
        Schema::rename('staffs', 'staff');

        // ── Restore staff table ───────────────────────────────────────────

        Schema::table('staff', function (Blueprint $table) {
            try { $table->dropForeign(['user_ID']); } catch (\Exception $e) {}
            $table->dropPrimary();
        });

        DB::statement('ALTER TABLE staff ADD COLUMN staff_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

        Schema::table('staff', function (Blueprint $table) {
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('cascade');
        });

        // ── Restore customers table ───────────────────────────────────────

        Schema::table('customers', function (Blueprint $table) {
            try { $table->dropForeign(['user_ID']); } catch (\Exception $e) {}
            $table->dropPrimary();
        });

        DB::statement('ALTER TABLE customers ADD COLUMN customer_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');

        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('cascade');
        });

        // ── Restore bookings foreign keys ─────────────────────────────────

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('customer_ID')
                  ->references('customer_ID')->on('customers')
                  ->onDelete('cascade');

            $table->foreign('approved_by')
                  ->references('staff_ID')->on('staff')
                  ->onDelete('set null');
        });

        // ── Restore payment foreign keys ──────────────────────────────────

        Schema::table('payment', function (Blueprint $table) {
            $table->foreign('verified_by')
                  ->references('staff_ID')->on('staff')
                  ->onDelete('set null');
        });
    }
};