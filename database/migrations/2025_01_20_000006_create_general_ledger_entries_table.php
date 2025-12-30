<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('general_ledger_entries')) {
            return;
        }
        
        Schema::create('general_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
            $table->morphs('source');
            $table->date('entry_date');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            // Note: fiscal_year_id and period_id foreign keys added in a later migration
            // to avoid dependency issues
            $table->unsignedBigInteger('fiscal_year_id')->nullable();
            $table->unsignedBigInteger('period_id')->nullable();
            $table->timestamps();
            
            $table->index('account_id');
            $table->index('entry_date');
            $table->index(['source_type', 'source_id']);
            $table->index('fiscal_year_id');
            $table->index('period_id');
            $table->index(['account_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_ledger_entries');
    }
};

