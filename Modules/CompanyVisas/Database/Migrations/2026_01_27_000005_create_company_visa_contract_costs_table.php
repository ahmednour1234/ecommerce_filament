<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_visa_contract_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->decimal('cost_per_worker', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('finance_entry_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('company_visa_contracts')->cascadeOnDelete();
            $table->foreign('finance_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_visa_contract_costs');
    }
};
