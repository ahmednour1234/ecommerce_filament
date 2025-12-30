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
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'posted'])->default('draft')->after('type');
            $table->foreignId('currency_id')->nullable()->after('amount')->constrained('currencies')->onDelete('restrict');
            $table->decimal('exchange_rate', 18, 8)->default(1)->after('currency_id');
            $table->decimal('base_amount', 15, 2)->nullable()->after('exchange_rate');
            // Note: project_id foreign key added after projects table exists
            $table->unsignedBigInteger('project_id')->nullable()->after('cost_center_id');
            $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            // Note: fiscal_year_id and period_id foreign keys added after those tables exist
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->after('voucher_date');
            $table->unsignedBigInteger('period_id')->nullable()->after('fiscal_year_id');
            
            // Bank Guarantee specific fields
            $table->enum('bg_type', ['issue', 'renew', 'release'])->nullable()->after('type');
            $table->date('bg_issue_date')->nullable();
            $table->date('bg_expiry_date')->nullable();
            $table->string('bg_number')->nullable();
            $table->unsignedBigInteger('bg_parent_id')->nullable()->after('bg_number');
            
            $table->index('status');
            $table->index('currency_id');
            $table->index('project_id');
            $table->index('fiscal_year_id');
            $table->index('period_id');
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

