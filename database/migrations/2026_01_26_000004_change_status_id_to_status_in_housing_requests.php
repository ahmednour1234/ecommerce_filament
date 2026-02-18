<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_requests')) {
            Schema::table('housing_requests', function (Blueprint $table) {
                // Drop foreign key constraint if exists
                $table->dropForeign(['status_id']);
            });

            // Migrate existing data if any (map status_id to status string)
            // This is optional - if there's existing data, we'd need to map it
            // For now, we'll just drop the column and add the new one
            
            Schema::table('housing_requests', function (Blueprint $table) {
                // Drop the old column
                $table->dropColumn('status_id');
            });

            Schema::table('housing_requests', function (Blueprint $table) {
                // Add new status column as string
                $table->string('status')->nullable()->after('request_date');
            });

            // Add index on status column
            Schema::table('housing_requests', function (Blueprint $table) {
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('housing_requests')) {
            Schema::table('housing_requests', function (Blueprint $table) {
                // Drop status column
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            });

            Schema::table('housing_requests', function (Blueprint $table) {
                // Add back status_id column
                $table->foreignId('status_id')->nullable()->after('request_date')->constrained('housing_statuses')->onDelete('set null');
            });

            Schema::table('housing_requests', function (Blueprint $table) {
                $table->index('status_id');
            });
        }
    }
};
