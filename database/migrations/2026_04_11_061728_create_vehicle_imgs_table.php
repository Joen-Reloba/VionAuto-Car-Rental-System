<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
                Schema::create('vehicle_imgs', function (Blueprint $table) {
            $table->id('vehicle_img_id');
            $table->unsignedBigInteger('vehicle_id'); // ← use this instead of foreignId()
            $table->string('img_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('vehicle_id')
                ->references('vehicle_ID') // ← points to the actual PK name
                ->on('vehicles')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_imgs');
    }
};