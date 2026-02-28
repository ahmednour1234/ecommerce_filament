<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('housing_buildings', function (Blueprint $table) {
            if (!Schema::hasColumn('housing_buildings', 'type')) {
                $table->enum('type', ['recruitment', 'rental'])->default('recruitment')->after('id');
                $table->index('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('housing_buildings', function (Blueprint $table) {
            if (Schema::hasColumn('housing_buildings', 'type')) {
                $table->dropIndex(['type']);
                $table->dropColumn('type');
            }
        });
    }
};
