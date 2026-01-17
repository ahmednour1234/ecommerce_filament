<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->foreignId('department_id')->nullable()->constrained('hr_departments')->onDelete('set null');
            $table->boolean('include_attendance_deductions')->default(true);
            $table->boolean('include_loan_installments')->default(true);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('generated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['year', 'month', 'department_id']);
            $table->index(['year', 'month']);
            $table->index('department_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_payroll_runs');
    }
};
