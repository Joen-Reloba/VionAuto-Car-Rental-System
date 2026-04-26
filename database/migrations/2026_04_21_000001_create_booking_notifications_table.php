<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedBigInteger('booking_ID');
            $table->foreign('booking_ID')->references('booking_ID')->on('bookings')->onDelete('cascade');
            $table->unsignedBigInteger('customer_user_id');
            $table->foreign('customer_user_id')->references('user_ID')->on('users')->onDelete('cascade');
            $table->enum('type', ['approved', 'rejected', 'pending'])->default('pending');
            $table->text('message')->nullable();
            $table->text('staff_note')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_notifications');
    }
};
