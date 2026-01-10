<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('loan_type_id')->constrained('hr_loan_types')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->unsignedInteger('installments_count');
            $table->decimal('installment_amount', 12, 2);
            $table->date('start_date');
            $table->text('purpose')->nullable();
            $table->string('attachment')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();

            $table->index('employee_id');
            $table->index('loan_type_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_loans');
    }
};
