<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('housing_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('housing_requests', 'passport_no')) {
                $table->string('passport_no')->nullable()->after('laborer_id');
            }
            if (!Schema::hasColumn('housing_requests', 'sponsor_name')) {
                $table->string('sponsor_name')->nullable()->after('passport_no');
            }
            if (!Schema::hasColumn('housing_requests', 'transferred_sponsor_name')) {
                $table->string('transferred_sponsor_name')->nullable()->after('sponsor_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('housing_requests', function (Blueprint $table) {
            if (Schema::hasColumn('housing_requests', 'transferred_sponsor_name')) {
                $table->dropColumn('transferred_sponsor_name');
            }
            if (Schema::hasColumn('housing_requests', 'sponsor_name')) {
                $table->dropColumn('sponsor_name');
            }
            if (Schema::hasColumn('housing_requests', 'passport_no')) {
                $table->dropColumn('passport_no');
            }
        });
    }
};
