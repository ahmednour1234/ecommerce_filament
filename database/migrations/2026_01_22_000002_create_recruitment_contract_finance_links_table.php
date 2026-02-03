<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('recruitment_contract_finance_links')) {
            return;
        }

        Schema::create('recruitment_contract_finance_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_contract_id')->constrained('recruitment_contracts')->cascadeOnDelete();
            $table->foreignId('finance_transaction_id')->nullable()->constrained('finance_branch_transactions')->nullOnDelete();
            $table->enum('type', ['receipt', 'expense']);
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->index('recruitment_contract_id');
            $table->index('finance_transaction_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_contract_finance_links');
    }
};
