<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'nationality_id')) {
                $table->foreignId('nationality_id')->nullable()->after('country_id')->constrained('nationalities')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            if (Schema::hasColumn('agents', 'nationality_id')) {
                $table->dropForeign(['nationality_id']);
                $table->dropColumn('nationality_id');
            }
        });
    }
};
