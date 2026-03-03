<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entries', 'old_sponsor_name')) {
                $table->string('old_sponsor_name')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entries', 'old_sponsor_name')) {
                $table->string('old_sponsor_name')->nullable(false)->change();
            }
        });
    }
};
