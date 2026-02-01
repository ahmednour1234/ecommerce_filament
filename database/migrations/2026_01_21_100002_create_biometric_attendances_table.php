<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('biometric_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('biometric_devices')->onDelete('cascade');
            $table->string('user_id');
            $table->dateTime('attended_at');
            $table->integer('state')->nullable();
            $table->integer('type')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('raw_data')->nullable();
            $table->boolean('processed')->default(false);
            $table->timestamps();

            $table->unique(['device_id', 'user_id', 'attended_at']);
            $table->index('device_id');
            $table->index('user_id');
            $table->index('attended_at');
            $table->index('processed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biometric_attendances');
    }
};
