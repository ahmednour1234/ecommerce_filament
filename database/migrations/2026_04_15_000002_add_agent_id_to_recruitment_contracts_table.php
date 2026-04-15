<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('recruitment_contracts', 'agent_id')) {
                $table->foreignId('agent_id')->nullable()->after('worker_id')->constrained('agents')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('recruitment_contracts', 'agent_id')) {
                $table->dropForeign(['agent_id']);
                $table->dropColumn('agent_id');
            }
        });
    }
};
