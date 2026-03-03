<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entries', 'nationality_id')) {
                $table->unsignedBigInteger('nationality_id')->nullable()->change();
            }
            if (Schema::hasColumn('accommodation_entries', 'worker_passport_number')) {
                $table->string('worker_passport_number')->nullable()->change();
            }
            if (Schema::hasColumn('accommodation_entries', 'new_sponsor_phone')) {
                $table->string('new_sponsor_phone')->nullable()->change();
            }
            if (Schema::hasColumn('accommodation_entries', 'old_sponsor_phone')) {
                $table->string('old_sponsor_phone')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entries', 'nationality_id')) {
                $table->unsignedBigInteger('nationality_id')->nullable(false)->change();
            }
            if (Schema::hasColumn('accommodation_entries', 'worker_passport_number')) {
                $table->string('worker_passport_number')->nullable(false)->change();
            }
            if (Schema::hasColumn('accommodation_entries', 'new_sponsor_phone')) {
                $table->string('new_sponsor_phone')->nullable(false)->change();
            }
            if (Schema::hasColumn('accommodation_entries', 'old_sponsor_phone')) {
                $table->string('old_sponsor_phone')->nullable(false)->change();
            }
        });
    }
};
