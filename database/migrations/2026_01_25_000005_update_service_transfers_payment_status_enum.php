<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('service_transfers', 'payment_status')) {
            DB::statement("ALTER TABLE service_transfers MODIFY COLUMN payment_status ENUM('pending', 'unpaid', 'partial', 'paid', 'refunded') DEFAULT 'unpaid'");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('service_transfers', 'payment_status')) {
            DB::statement("ALTER TABLE service_transfers MODIFY COLUMN payment_status ENUM('unpaid', 'partial', 'paid', 'refunded') DEFAULT 'unpaid'");
        }
    }
};
