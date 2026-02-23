<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_transfers', function (Blueprint $table) {
            $table->enum('status', [
                'transferred',
                'cancelled',
                'in_trial',
                'multiple_trial',
                'no_action_taken'
            ])->nullable()->after('request_status');
            
            $table->date('trial_end_date')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('service_transfers', function (Blueprint $table) {
            $table->dropColumn(['status', 'trial_end_date']);
        });
    }
};
