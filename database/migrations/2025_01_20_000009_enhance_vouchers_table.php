<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // Add new columns first
            if (!Schema::hasColumn('vouchers', 'status')) {
                $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'posted'])->default('draft');
            }
            if (!Schema::hasColumn('vouchers', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)->default(1);
            }
            if (!Schema::hasColumn('vouchers', 'base_amount')) {
                $table->decimal('base_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'fiscal_year_id')) {
                $table->unsignedBigInteger('fiscal_year_id')->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'period_id')) {
                $table->unsignedBigInteger('period_id')->nullable();
            }
            
            // Bank Guarantee specific fields
            if (!Schema::hasColumn('vouchers', 'bg_type')) {
                $table->enum('bg_type', ['issue', 'renew', 'release'])->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'bg_issue_date')) {
                $table->date('bg_issue_date')->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'bg_expiry_date')) {
                $table->date('bg_expiry_date')->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'bg_number')) {
                $table->string('bg_number')->nullable();
            }
            if (!Schema::hasColumn('vouchers', 'bg_parent_id')) {
                $table->unsignedBigInteger('bg_parent_id')->nullable();
            }
        });
        
        // Add foreign keys
        Schema::table('vouchers', function (Blueprint $table) {
            try {
                if (Schema::hasColumn('vouchers', 'currency_id') && Schema::hasTable('currencies')) {
                    $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('restrict');
                }
                if (Schema::hasColumn('vouchers', 'approved_by')) {
                    $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                }
            } catch (\Exception $e) {
                // Foreign keys might already exist
            }
        });
        
        // Add indexes
        Schema::table('vouchers', function (Blueprint $table) {
            try {
                if (Schema::hasColumn('vouchers', 'status')) {
                    $table->index('status');
                }
                if (Schema::hasColumn('vouchers', 'currency_id')) {
                    $table->index('currency_id');
                }
                if (Schema::hasColumn('vouchers', 'project_id')) {
                    $table->index('project_id');
                }
                if (Schema::hasColumn('vouchers', 'fiscal_year_id')) {
                    $table->index('fiscal_year_id');
                }
                if (Schema::hasColumn('vouchers', 'period_id')) {
                    $table->index('period_id');
                }
            } catch (\Exception $e) {
                // Indexes might already exist
            }
        });
        
        // Modify type enum using raw SQL (MySQL doesn't support direct enum modification)
        // Only if the column exists
        if (Schema::hasColumn('vouchers', 'type')) {
            try {
                DB::statement("ALTER TABLE vouchers MODIFY COLUMN type ENUM('payment', 'receipt', 'bank_guarantee')");
            } catch (\Exception $e) {
                // If it fails, the enum might already be correct or table doesn't exist
            }
        }
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['fiscal_year_id']);
            $table->dropForeign(['period_id']);
            $table->dropForeign(['bg_parent_id']);
            $table->dropColumn([
                'status', 'currency_id', 'exchange_rate', 'base_amount', 'project_id',
                'approved_by', 'approved_at', 'fiscal_year_id', 'period_id',
                'bg_type', 'bg_issue_date', 'bg_expiry_date', 'bg_number', 'bg_parent_id'
            ]);
        });
        
        Schema::table('vouchers', function (Blueprint $table) {
            $table->enum('type', ['payment', 'receipt'])->after('voucher_number')->change();
        });
    }
};

