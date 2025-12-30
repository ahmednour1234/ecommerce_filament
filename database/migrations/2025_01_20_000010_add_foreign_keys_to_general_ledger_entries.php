<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('general_ledger_entries', function (Blueprint $table) {
            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('restrict');
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('general_ledger_entries', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropForeign(['period_id']);
        });
    }
};

