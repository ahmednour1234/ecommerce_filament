<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the foreign key constraint on nationality_id so it no longer
        // requires a matching record in the nationalities table.
        // The column is kept as a plain nullable integer for reference only.
        if (Schema::hasColumn('accommodation_entries', 'nationality_id')) {
            // Check if the foreign key actually exists before trying to drop it
            $hasFk = collect(
                DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                      AND TABLE_NAME   = 'accommodation_entries'
                      AND COLUMN_NAME  = 'nationality_id'
                      AND REFERENCED_TABLE_NAME IS NOT NULL
                ")
            )->isNotEmpty();

            if ($hasFk) {
                Schema::table('accommodation_entries', function (Blueprint $table) {
                    $table->dropForeign(['nationality_id']);
                });
            }
        }
    }

    public function down(): void
    {
        // Re-add FK only if it was originally intended (optional)
        if (Schema::hasColumn('accommodation_entries', 'nationality_id')) {
            Schema::table('accommodation_entries', function (Blueprint $table) {
                $table->foreign('nationality_id')
                      ->references('id')
                      ->on('nationalities')
                      ->nullOnDelete();
            });
        }
    }
};
