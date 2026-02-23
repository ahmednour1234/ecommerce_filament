<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('accommodation_entries', 'exit_date')) {
                $table->datetime('exit_date')->nullable()->after('entry_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entries', 'exit_date')) {
                $table->dropColumn('exit_date');
            }
        });
    }
};
