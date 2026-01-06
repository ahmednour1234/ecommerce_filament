<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_guarantees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->string('guarantee_number')->unique()->nullable();
            $table->date('issue_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('beneficiary_name');
            $table->decimal('amount', 15, 2);
            $table->decimal('bank_fees', 15, 2)->default(0);
            $table->foreignId('original_guarantee_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignId('bank_account_id')->constrained('accounts')->onDelete('restrict');
            $table->foreignId('bank_fees_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignId('bank_fees_debit_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->string('attachment_path')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('branch_id');
            $table->index('guarantee_number');
            $table->index('status');
            $table->index('end_date');
            $table->index('beneficiary_name');
            $table->index('original_guarantee_account_id');
            $table->index('bank_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_guarantees');
    }
};

