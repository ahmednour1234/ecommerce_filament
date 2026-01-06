<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hr_work_places', function (Blueprint $table) {
            $table->foreignId('default_schedule_id')->nullable()->after('radius_meters')
                ->constrained('hr_work_schedules')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hr_work_places', function (Blueprint $table) {
            $table->dropForeign(['default_schedule_id']);
            $table->dropColumn('default_schedule_id');
        });
    }
};

