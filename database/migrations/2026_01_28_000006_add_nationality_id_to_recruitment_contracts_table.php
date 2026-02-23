<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('recruitment_contracts', 'nationality_id')) {
                $table->foreignId('nationality_id')->nullable()->after('profession_id')->constrained('nationalities')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('recruitment_contracts', 'nationality_id')) {
                $table->dropForeign(['nationality_id']);
                $table->dropColumn('nationality_id');
            }
        });
    }
};
