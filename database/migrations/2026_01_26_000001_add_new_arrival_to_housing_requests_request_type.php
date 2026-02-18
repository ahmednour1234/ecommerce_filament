<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_requests') && Schema::hasColumn('housing_requests', 'request_type')) {
            DB::statement("ALTER TABLE housing_requests MODIFY COLUMN request_type ENUM('delivery', 'return', 'new_arrival') DEFAULT 'delivery'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('housing_requests') && Schema::hasColumn('housing_requests', 'request_type')) {
            DB::statement("ALTER TABLE housing_requests MODIFY COLUMN request_type ENUM('delivery', 'return') DEFAULT 'delivery'");
        }
    }
};
