<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_branch_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('trx_date')->index();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->unsignedInteger('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('finance_type_id')->constrained('finance_types')->restrictOnDelete();
            $table->decimal('amount', 18, 2);
            $table->string('payment_method', 50)->nullable();
            $table->string('recipient_name', 150)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment_path', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['branch_id', 'trx_date']);
            $table->index(['finance_type_id', 'trx_date']);
            $table->index(['currency_id', 'trx_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_branch_transactions');
    }
};
