<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branch_transactions', function (Blueprint $table) {
            $table->id();

            // document
            $table->string('document_no', 50)->unique();

            // relations
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();

            // type & money
            $table->enum('type', ['income', 'expense'])->index();
            $table->decimal('amount', 15, 2);
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->decimal('amount_base', 15, 2)->nullable(); // بعد التحويل للعملة الافتراضية
            $table->decimal('rate_used', 18, 8)->nullable();   // سعر التحويل وقت الإنشاء
            $table->date('transaction_date')->index();

            // receiver
            $table->string('receiver_name')->nullable();
            $table->string('payment_method')->nullable(); // cash, bank, ...
            $table->string('reference_no')->nullable();   // رقم إيصال/تحويل

            // notes + attachment
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();

            // status (approval)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();

            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // performance
            $table->index(['branch_id', 'status']);
            $table->index(['branch_id', 'transaction_date']);
            $table->index(['country_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_transactions');
    }
};
