<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('accommodation_entries', 'new_sponsor_name')) {
                $table->string('new_sponsor_name')->nullable()->after('exit_date');
            }
            if (!Schema::hasColumn('accommodation_entries', 'old_sponsor_name')) {
                $table->string('old_sponsor_name')->nullable()->after('new_sponsor_name');
            }
            if (!Schema::hasColumn('accommodation_entries', 'nationality_id')) {
                $table->unsignedBigInteger('nationality_id')->nullable()->after('old_sponsor_name');
                $table->foreign('nationality_id')->references('id')->on('nationalities')->onDelete('set null');
            }
            if (!Schema::hasColumn('accommodation_entries', 'worker_passport_number')) {
                $table->string('worker_passport_number')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('accommodation_entries', 'new_sponsor_phone')) {
                $table->string('new_sponsor_phone')->nullable()->after('worker_passport_number');
            }
            if (!Schema::hasColumn('accommodation_entries', 'old_sponsor_phone')) {
                $table->string('old_sponsor_phone')->nullable()->after('new_sponsor_phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entries', 'new_sponsor_name')) {
                $table->dropColumn('new_sponsor_name');
            }
            if (Schema::hasColumn('accommodation_entries', 'old_sponsor_name')) {
                $table->dropColumn('old_sponsor_name');
            }
            if (Schema::hasColumn('accommodation_entries', 'nationality_id')) {
                $table->dropForeign(['nationality_id']);
                $table->dropColumn('nationality_id');
            }
            if (Schema::hasColumn('accommodation_entries', 'worker_passport_number')) {
                $table->dropColumn('worker_passport_number');
            }
            if (Schema::hasColumn('accommodation_entries', 'new_sponsor_phone')) {
                $table->dropColumn('new_sponsor_phone');
            }
            if (Schema::hasColumn('accommodation_entries', 'old_sponsor_phone')) {
                $table->dropColumn('old_sponsor_phone');
            }
        });
    }
};
