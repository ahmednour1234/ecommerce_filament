<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('installment_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installment_id')->constrained('installments')->cascadeOnDelete();
            $table->integer('installment_number'); // 1, 2, 3, etc.
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->decimal('amount', 18, 2);
            $table->decimal('principal', 18, 2);
            $table->decimal('interest', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('remaining_amount', 18, 2);
            $table->string('status')->default('pending'); // pending, paid, partial, overdue, waived
            $table->foreignId('payment_transaction_id')->nullable()->constrained('payment_transactions')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('installment_id');
            $table->index('due_date');
            $table->index('status');
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_payments');
    }
};

