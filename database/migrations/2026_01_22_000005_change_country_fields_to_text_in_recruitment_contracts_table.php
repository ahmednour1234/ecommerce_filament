<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropForeign(['arrival_country_id']);
            $table->dropForeign(['departure_country_id']);
            $table->dropForeign(['receiving_station_id']);
            $table->dropIndex(['arrival_country_id']);
            $table->dropIndex(['departure_country_id']);
            $table->dropIndex(['receiving_station_id']);
        });

        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->string('arrival_country_id')->nullable()->change();
            $table->string('departure_country_id')->nullable()->change();
            $table->string('receiving_station_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->unsignedInteger('arrival_country_id')->nullable()->change();
            $table->unsignedInteger('departure_country_id')->nullable()->change();
            $table->unsignedInteger('receiving_station_id')->nullable()->change();
        });

        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->foreign('arrival_country_id')->references('id')->on('countries')->restrictOnDelete();
            $table->foreign('departure_country_id')->references('id')->on('countries')->restrictOnDelete();
            $table->foreign('receiving_station_id')->references('id')->on('countries')->nullOnDelete();
            $table->index('arrival_country_id');
            $table->index('departure_country_id');
            $table->index('receiving_station_id');
        });
    }
};
