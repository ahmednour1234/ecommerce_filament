<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add composite indexes for journal_entries table
        Schema::table('journal_entries', function (Blueprint $table) {
            // Composite index for common query patterns
            try {
                $table->index(['entry_date', 'is_posted'], 'idx_entry_date_posted');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['status', 'is_posted'], 'idx_status_posted');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['journal_id', 'entry_date'], 'idx_journal_date');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['branch_id', 'entry_date'], 'idx_branch_date');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['period_id', 'is_posted'], 'idx_period_posted');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['user_id', 'entry_date'], 'idx_user_date');
            } catch (\Exception $e) {
                // Index might already exist
            }
        });

        // Add composite indexes for journal_entry_lines table
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            // Composite index for balance calculations
            try {
                $table->index(['journal_entry_id', 'debit'], 'idx_entry_debit');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['journal_entry_id', 'credit'], 'idx_entry_credit');
            } catch (\Exception $e) {
                // Index might already exist
            }

            // Composite index for account queries
            try {
                $table->index(['account_id', 'journal_entry_id'], 'idx_account_entry');
            } catch (\Exception $e) {
                // Index might already exist
            }

            // Composite index for base_amount calculations
            try {
                $table->index(['journal_entry_id', 'base_amount'], 'idx_entry_base_amount');
            } catch (\Exception $e) {
                // Index might already exist
            }

            // Composite index for branch and account queries
            try {
                $table->index(['branch_id', 'account_id'], 'idx_branch_account');
            } catch (\Exception $e) {
                // Index might already exist
            }

            // Composite index for cost center queries
            try {
                $table->index(['cost_center_id', 'account_id'], 'idx_cost_center_account');
            } catch (\Exception $e) {
                // Index might already exist
            }
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex('idx_entry_date_posted');
            $table->dropIndex('idx_status_posted');
            $table->dropIndex('idx_journal_date');
            $table->dropIndex('idx_branch_date');
            $table->dropIndex('idx_period_posted');
            $table->dropIndex('idx_user_date');
        });

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropIndex('idx_entry_debit');
            $table->dropIndex('idx_entry_credit');
            $table->dropIndex('idx_account_entry');
            $table->dropIndex('idx_entry_base_amount');
            $table->dropIndex('idx_branch_account');
            $table->dropIndex('idx_cost_center_account');
        });
    }
};

