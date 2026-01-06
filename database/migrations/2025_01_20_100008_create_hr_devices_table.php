<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['fingerprint'])->default('fingerprint');
            $table->string('ip')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('api_key')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('status');
            $table->index('api_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_devices');
    }
};

