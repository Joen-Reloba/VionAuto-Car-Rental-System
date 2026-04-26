<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_ID');
            $table->unsignedBigInteger('vehicle_ID');
            $table->foreign('vehicle_ID')->references('vehicle_ID')->on('vehicles')->onDelete('cascade');
            $table->unsignedBigInteger('customer_ID');
            $table->foreign('customer_ID')->references('customer_ID')->on('customers')->onDelete('cascade');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('staff_ID')->on('staff')->onDelete('set null');
            $table->date('rent_start');
            $table->date('rent_end');
            $table->decimal('downpayment', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'ongoing', 'finished'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'downpaid', 'fullpaid'])->default('unpaid');
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};