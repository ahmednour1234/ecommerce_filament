<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add housing_type to housing_requests (rename existing type to request_type first)
        if (Schema::hasTable('housing_requests')) {
            if (Schema::hasColumn('housing_requests', 'type') && !Schema::hasColumn('housing_requests', 'request_type')) {
                // Add new request_type column
                Schema::table('housing_requests', function (Blueprint $table) {
                    $table->enum('request_type', ['delivery', 'return'])->default('delivery')->after('laborer_id');
                });
                
                // Copy data from type to request_type
                \DB::statement('UPDATE housing_requests SET request_type = type');
                
                // Drop old type column
                Schema::table('housing_requests', function (Blueprint $table) {
                    $table->dropColumn('type');
                });
            }
            
            if (!Schema::hasColumn('housing_requests', 'housing_type')) {
                Schema::table('housing_requests', function (Blueprint $table) {
                    $table->enum('housing_type', ['recruitment', 'rental'])->default('recruitment')->after('laborer_id');
                });
            }
        }

        // Add type to housing_salaries
        if (Schema::hasTable('housing_salaries') && !Schema::hasColumn('housing_salaries', 'type')) {
            Schema::table('housing_salaries', function (Blueprint $table) {
                $table->enum('type', ['recruitment', 'rental'])->default('recruitment')->after('employee_id');
            });
        }

        // Add type to housing_leaves
        if (Schema::hasTable('housing_leaves') && !Schema::hasColumn('housing_leaves', 'type')) {
            Schema::table('housing_leaves', function (Blueprint $table) {
                $table->enum('type', ['recruitment', 'rental'])->default('recruitment')->after('employee_id');
            });
        }

        // Add type to accommodation_entries
        if (Schema::hasTable('accommodation_entries') && !Schema::hasColumn('accommodation_entries', 'type')) {
            Schema::table('accommodation_entries', function (Blueprint $table) {
                $table->enum('type', ['recruitment', 'rental'])->default('recruitment')->after('laborer_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('housing_requests')) {
            if (Schema::hasColumn('housing_requests', 'housing_type')) {
                Schema::table('housing_requests', function (Blueprint $table) {
                    $table->dropColumn('housing_type');
                });
            }
            
            if (Schema::hasColumn('housing_requests', 'request_type') && !Schema::hasColumn('housing_requests', 'type')) {
                // Add type column back
                Schema::table('housing_requests', function (Blueprint $table) {
                    $table->enum('type', ['delivery', 'return'])->default('delivery')->after('laborer_id');
                });
                
                // Copy data from request_type to type
                \DB::statement('UPDATE housing_requests SET type = request_type');
                
                // Drop request_type column
                Schema::table('housing_requests', function (Blueprint $table) {
                    $table->dropColumn('request_type');
                });
            }
        }

        if (Schema::hasTable('housing_salaries') && Schema::hasColumn('housing_salaries', 'type')) {
            Schema::table('housing_salaries', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasTable('housing_leaves') && Schema::hasColumn('housing_leaves', 'type')) {
            Schema::table('housing_leaves', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasTable('accommodation_entries') && Schema::hasColumn('accommodation_entries', 'type')) {
            Schema::table('accommodation_entries', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
