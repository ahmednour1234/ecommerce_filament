<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rental_contract_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_contract_id')->constrained('rental_contracts')->cascadeOnDelete();
            $table->foreignId('finance_transaction_id')->nullable()->constrained('finance_branch_transactions')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->dateTime('paid_at');
            $table->unsignedBigInteger('method_id')->nullable();
            $table->string('method')->nullable();
            $table->string('reference')->nullable();
            $table->enum('status', ['pending', 'posted', 'void', 'refunded'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('rental_contract_id');
            $table->index('finance_transaction_id');
            $table->index('status');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_contract_payments');
    }
};
