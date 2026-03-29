<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('accommodation_entries', 'status_keys')) {
                $table->json('status_keys')->nullable()->after('status_key');
            }
        });

        // Backfill old single status into the new JSON field.
        DB::table('accommodation_entries')
            ->whereNotNull('status_key')
            ->where(function ($query) {
                $query->whereNull('status_keys')->orWhere('status_keys', '');
            })
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('accommodation_entries')
                        ->where('id', $row->id)
                        ->update(['status_keys' => json_encode([$row->status_key])]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entries', 'status_keys')) {
                $table->dropColumn('status_keys');
            }
        });
    }
};
