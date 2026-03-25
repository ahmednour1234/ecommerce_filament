<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('accommodation_entries') && !Schema::hasColumn('accommodation_entries', 'status_key')) {
            Schema::table('accommodation_entries', function (Blueprint $table) {
                $table->string('status_key')->nullable()->after('status_id');
                $table->index('status_key');
            });
        }

        if (Schema::hasTable('accommodation_entry_status_logs') && !Schema::hasColumn('accommodation_entry_status_logs', 'status_key')) {
            Schema::table('accommodation_entry_status_logs', function (Blueprint $table) {
                $table->string('status_key')->nullable()->after('new_status_id');
                $table->index('status_key');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('accommodation_entries') && Schema::hasColumn('accommodation_entries', 'status_key')) {
            Schema::table('accommodation_entries', function (Blueprint $table) {
                $table->dropIndex(['status_key']);
                $table->dropColumn('status_key');
            });
        }

        if (Schema::hasTable('accommodation_entry_status_logs') && Schema::hasColumn('accommodation_entry_status_logs', 'status_key')) {
            Schema::table('accommodation_entry_status_logs', function (Blueprint $table) {
                $table->dropIndex(['status_key']);
                $table->dropColumn('status_key');
            });
        }
    }
};
