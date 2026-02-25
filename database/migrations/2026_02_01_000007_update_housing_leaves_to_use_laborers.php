<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('housing_leaves')) {
            return;
        }

        Schema::table('housing_leaves', function (Blueprint $table) {
            if (Schema::hasColumn('housing_leaves', 'employee_id')) {
                $table->dropForeign(['employee_id']);
            }
        });

        if (Schema::hasColumn('housing_leaves', 'employee_id')) {
            Schema::table('housing_leaves', function (Blueprint $table) {
                $table->dropColumn('employee_id');
            });
        }

        if (!Schema::hasColumn('housing_leaves', 'laborer_id')) {
            Schema::table('housing_leaves', function (Blueprint $table) {
                $table->foreignId('laborer_id')->nullable()->after('id')->constrained('laborers')->onDelete('cascade');
            });
        }

        if (Schema::hasColumn('housing_leaves', 'leave_type_id')) {
            Schema::table('housing_leaves', function (Blueprint $table) {
                $table->dropForeign(['leave_type_id']);
                $table->dropColumn('leave_type_id');
            });
        }

        if (!Schema::hasColumn('housing_leaves', 'leave_type')) {
            Schema::table('housing_leaves', function (Blueprint $table) {
                $table->string('leave_type')->nullable()->after('laborer_id');
            });
        }

        if (!Schema::hasColumn('housing_leaves', 'return_registered_at')) {
            Schema::table('housing_leaves', function (Blueprint $table) {
                $table->timestamp('return_registered_at')->nullable()->after('approved_at');
            });
        }

        if (!Schema::hasColumn('housing_leaves', 'deleted_at')) {
            Schema::table('housing_leaves', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        Schema::table('housing_leaves', function (Blueprint $table) {
            $table->index('laborer_id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('housing_leaves')) {
            return;
        }

        Schema::table('housing_leaves', function (Blueprint $table) {
            if (Schema::hasColumn('housing_leaves', 'laborer_id')) {
                $table->dropForeign(['laborer_id']);
                $table->dropColumn('laborer_id');
            }

            if (Schema::hasColumn('housing_leaves', 'leave_type')) {
                $table->dropColumn('leave_type');
            }

            if (Schema::hasColumn('housing_leaves', 'return_registered_at')) {
                $table->dropColumn('return_registered_at');
            }

            if (Schema::hasColumn('housing_leaves', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            if (!Schema::hasColumn('housing_leaves', 'employee_id')) {
                $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            }

            if (!Schema::hasColumn('housing_leaves', 'leave_type_id')) {
                $table->foreignId('leave_type_id')->constrained('hr_leave_types')->onDelete('restrict');
            }
        });
    }
};
