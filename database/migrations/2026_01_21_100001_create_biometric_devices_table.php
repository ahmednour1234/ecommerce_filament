<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('biometric_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_number')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('api_key')->unique();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('api_key');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biometric_devices');
    }
};
