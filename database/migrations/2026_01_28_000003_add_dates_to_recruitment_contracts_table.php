<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('recruitment_contracts', 'arrival_date')) {
                $table->date('arrival_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('recruitment_contracts', 'trial_end_date')) {
                $table->date('trial_end_date')->nullable()->after('arrival_date');
            }
            if (!Schema::hasColumn('recruitment_contracts', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable()->after('trial_end_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropColumn(['arrival_date', 'trial_end_date', 'contract_end_date']);
        });
    }
};
