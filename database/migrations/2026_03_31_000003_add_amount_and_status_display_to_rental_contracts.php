<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            // Ensure amount column exists (added in 000002 migration — safe to re-run)
            if (!Schema::hasColumn('rental_contracts', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0)->after('package_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('rental_contracts', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }
};
