<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_cars')) {
            return;
        }

        Schema::create('housing_cars', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['recruitment', 'rental'])->default('recruitment');
            $table->string('car_type');
            $table->string('car_model');
            $table->string('plate_number')->unique();
            $table->string('serial_number');
            $table->foreignId('driver_id')->nullable()->constrained('housing_drivers')->onDelete('set null');
            $table->date('insurance_expiry_date');
            $table->date('inspection_expiry_date');
            $table->date('form_expiry_date');
            $table->string('car_form_file')->nullable();
            $table->text('driver_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('plate_number');
            $table->index('driver_id');
            $table->index('insurance_expiry_date');
            $table->index('inspection_expiry_date');
            $table->index('form_expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_cars');
    }
};
