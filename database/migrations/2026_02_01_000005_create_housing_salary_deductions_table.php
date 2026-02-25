<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_salary_deductions')) {
            return;
        }

        Schema::create('housing_salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laborer_id')->constrained('laborers')->onDelete('cascade');
            $table->date('deduction_date');
            $table->string('deduction_type');
            $table->decimal('amount', 12, 2);
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'applied'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index('laborer_id');
            $table->index('deduction_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_salary_deductions');
    }
};
