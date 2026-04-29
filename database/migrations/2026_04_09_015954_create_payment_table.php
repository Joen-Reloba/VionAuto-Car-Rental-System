<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_ID');
            $table->unsignedBigInteger('booking_ID');
            $table->foreign('booking_ID')->references('booking_ID')->on('bookings')->onDelete('cascade');
            $table->unsignedBigInteger('verified_by_user_id')->nullable();
            $table->foreign('verified_by_user_id')->references('user_ID')->on('users')->onDelete('set null');
            $table->enum('payment_type', ['downpayment', 'final']);
            $table->string('reference_number')->nullable();
            $table->string('receipt_image')->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};