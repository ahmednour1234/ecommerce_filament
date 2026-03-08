<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'in_progress_at')) {
                $table->timestamp('in_progress_at')->nullable()->after('resolved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (Schema::hasColumn('complaints', 'in_progress_at')) {
                $table->dropColumn('in_progress_at');
            }
        });
    }
};
