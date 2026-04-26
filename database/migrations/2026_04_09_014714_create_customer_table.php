<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_ID');
            $table->unsignedBigInteger('user_ID');
            $table->foreign('user_ID')->references('user_ID')->on('users')->onDelete('cascade');
            $table->date('birthday')->nullable();
            $table->string('license_no')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('address')->nullable();
            $table->string('valid_ID')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};