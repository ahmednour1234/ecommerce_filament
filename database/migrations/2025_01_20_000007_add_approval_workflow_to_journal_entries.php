<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'status')) {
                $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'posted'])->default('draft');
            }
            if (!Schema::hasColumn('journal_entries', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable();
            }
            if (!Schema::hasColumn('journal_entries', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('journal_entries', 'rejected_by')) {
                $table->unsignedBigInteger('rejected_by')->nullable();
            }
            if (!Schema::hasColumn('journal_entries', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
            if (!Schema::hasColumn('journal_entries', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
            if (!Schema::hasColumn('journal_entries', 'fiscal_year_id')) {
                $table->unsignedBigInteger('fiscal_year_id')->nullable();
            }
            if (!Schema::hasColumn('journal_entries', 'period_id')) {
                $table->unsignedBigInteger('period_id')->nullable();
            }
        });
        
        // Add foreign keys for user relationships
        Schema::table('journal_entries', function (Blueprint $table) {
            try {
                if (Schema::hasColumn('journal_entries', 'approved_by')) {
                    $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                }
                if (Schema::hasColumn('journal_entries', 'rejected_by')) {
                    $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
                }
            } catch (\Exception $e) {
                // Foreign keys might already exist
            }
        });
        
        // Add foreign keys and indexes if tables exist
        if (Schema::hasTable('fiscal_years') && Schema::hasTable('periods')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                try {
                    if (Schema::hasColumn('journal_entries', 'fiscal_year_id')) {
                        $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('restrict');
                    }
                    if (Schema::hasColumn('journal_entries', 'period_id')) {
                        $table->foreign('period_id')->references('id')->on('periods')->onDelete('restrict');
                    }
                } catch (\Exception $e) {
                    // Foreign keys might already exist
                }
            });
        }
        
        // Add indexes
        Schema::table('journal_entries', function (Blueprint $table) {
            if (Schema::hasColumn('journal_entries', 'status')) {
                try {
                    $table->index('status');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
            if (Schema::hasColumn('journal_entries', 'fiscal_year_id')) {
                try {
                    $table->index('fiscal_year_id');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
            if (Schema::hasColumn('journal_entries', 'period_id')) {
                try {
                    $table->index('period_id');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['fiscal_year_id']);
            $table->dropForeign(['period_id']);
            $table->dropColumn([
                'status', 'approved_by', 'approved_at', 
                'rejected_by', 'rejected_at', 'rejection_reason',
                'fiscal_year_id', 'period_id'
            ]);
        });
    }
};

