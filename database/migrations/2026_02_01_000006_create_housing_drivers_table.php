<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_drivers')) {
            return;
        }

        Schema::create('housing_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('identity_number')->unique();
            $table->string('license_number')->unique();
            $table->date('license_expiry');
            $table->string('car_type')->nullable();
            $table->string('car_model')->nullable();
            $table->string('plate_number')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('phone');
            $table->index('license_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_drivers');
    }
};
