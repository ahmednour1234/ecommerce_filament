<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('housing_drivers', function (Blueprint $table) {
            if (Schema::hasColumn('housing_drivers', 'car_type')) {
                $table->dropColumn('car_type');
            }
            if (Schema::hasColumn('housing_drivers', 'car_model')) {
                $table->dropColumn('car_model');
            }
            if (Schema::hasColumn('housing_drivers', 'plate_number')) {
                $table->dropColumn('plate_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('housing_drivers', function (Blueprint $table) {
            $table->string('car_type')->nullable()->after('license_expiry');
            $table->string('car_model')->nullable()->after('car_type');
            $table->string('plate_number')->nullable()->after('car_model');
        });
    }
};
