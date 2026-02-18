<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_visa_contract_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('expense_account_id');
            $table->decimal('amount', 12, 2);
            $table->boolean('includes_vat')->default(false);
            $table->date('expense_date');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('attachment_path')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('finance_entry_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('company_visa_contracts')->cascadeOnDelete();
            $table->foreign('expense_account_id')->references('id')->on('accounts')->restrictOnDelete();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->nullOnDelete();
            $table->foreign('finance_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_visa_contract_expenses');
    }
};
