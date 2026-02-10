<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->unsignedInteger('receiving_station_id')->nullable()->after('departure_country_id');
            $table->foreign('receiving_station_id')->references('id')->on('countries')->nullOnDelete();
            $table->index('receiving_station_id');
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropForeign(['receiving_station_id']);
            $table->dropIndex(['receiving_station_id']);
            $table->dropColumn('receiving_station_id');
        });
    }
};
