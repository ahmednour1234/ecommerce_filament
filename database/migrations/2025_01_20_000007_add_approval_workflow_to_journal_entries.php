<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'posted'])->default('draft')->after('is_posted');
            $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->foreignId('rejected_by')->nullable()->after('approved_at')->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            $table->foreignId('fiscal_year_id')->nullable()->after('entry_date')->constrained('fiscal_years')->onDelete('restrict');
            $table->foreignId('period_id')->nullable()->after('fiscal_year_id')->constrained('periods')->onDelete('restrict');
            
            $table->index('status');
            $table->index('fiscal_year_id');
            $table->index('period_id');
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

