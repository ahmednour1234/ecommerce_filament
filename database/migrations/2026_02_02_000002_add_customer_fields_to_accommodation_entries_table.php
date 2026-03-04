<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('accommodation_entries', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('old_sponsor_phone');
            }
            if (!Schema::hasColumn('accommodation_entries', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('accommodation_entries', 'customer_id_number')) {
                $table->string('customer_id_number')->nullable()->after('customer_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entries', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            if (Schema::hasColumn('accommodation_entries', 'customer_phone')) {
                $table->dropColumn('customer_phone');
            }
            if (Schema::hasColumn('accommodation_entries', 'customer_id_number')) {
                $table->dropColumn('customer_id_number');
            }
        });
    }
};
