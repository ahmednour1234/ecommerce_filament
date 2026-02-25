<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('housing_requests')) {
            return;
        }

        if (Schema::hasColumn('housing_requests', 'request_type')) {
            DB::statement("ALTER TABLE housing_requests MODIFY COLUMN request_type ENUM('new_rent', 'cancel_rent', 'transfer_kafala', 'outside_service', 'leave_request', 'delivery', 'return', 'new_arrival') DEFAULT 'new_rent'");
        }

        Schema::table('housing_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('housing_requests', 'requested_from')) {
                $table->date('requested_from')->nullable()->after('request_date');
            }

            if (!Schema::hasColumn('housing_requests', 'requested_to')) {
                $table->date('requested_to')->nullable()->after('requested_from');
            }

            if (!Schema::hasColumn('housing_requests', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('housing_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('housing_requests', 'building_id')) {
                $table->foreignId('building_id')->nullable()->after('laborer_id')->constrained('housing_buildings')->onDelete('set null');
            }

            if (!Schema::hasColumn('housing_requests', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('building_id')->constrained('housing_units')->onDelete('set null');
            }
        });

        Schema::table('housing_requests', function (Blueprint $table) {
            if (Schema::hasColumn('housing_requests', 'requested_from')) {
                $table->index('requested_from');
            }
            if (Schema::hasColumn('housing_requests', 'requested_to')) {
                $table->index('requested_to');
            }
            if (Schema::hasColumn('housing_requests', 'building_id')) {
                $table->index('building_id');
            }
            if (Schema::hasColumn('housing_requests', 'unit_id')) {
                $table->index('unit_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('housing_requests')) {
            return;
        }

        Schema::table('housing_requests', function (Blueprint $table) {
            if (Schema::hasColumn('housing_requests', 'requested_from')) {
                $table->dropIndex(['requested_from']);
                $table->dropColumn('requested_from');
            }

            if (Schema::hasColumn('housing_requests', 'requested_to')) {
                $table->dropIndex(['requested_to']);
                $table->dropColumn('requested_to');
            }

            if (Schema::hasColumn('housing_requests', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }

            if (Schema::hasColumn('housing_requests', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('housing_requests', 'building_id')) {
                $table->dropIndex(['building_id']);
                $table->dropForeign(['building_id']);
                $table->dropColumn('building_id');
            }

            if (Schema::hasColumn('housing_requests', 'unit_id')) {
                $table->dropIndex(['unit_id']);
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
        });
    }
};
