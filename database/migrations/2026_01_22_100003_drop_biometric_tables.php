<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('biometric_attendances')) {
            Schema::dropIfExists('biometric_attendances');
        }

        if (Schema::hasTable('biometric_devices')) {
            Schema::dropIfExists('biometric_devices');
        }
    }

    public function down(): void
    {
    }
};
