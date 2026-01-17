<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_payroll_run_item_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_item_id')->constrained('hr_payroll_run_items')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('hr_salary_components')->onDelete('cascade');
            $table->enum('type', ['earning', 'deduction']);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();

            $table->index('payroll_run_item_id');
            $table->index('component_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_payroll_run_item_lines');
    }
};
