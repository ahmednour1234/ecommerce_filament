<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accommodation_entry_status_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('accommodation_entry_status_logs', 'attachment')) {
                $table->string('attachment')->nullable()->after('status_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accommodation_entry_status_logs', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entry_status_logs', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};
