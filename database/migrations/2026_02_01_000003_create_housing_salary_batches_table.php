<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_salary_batches')) {
            return;
        }

        Schema::create('housing_salary_batches', function (Blueprint $table) {
            $table->id();
            $table->string('month', 7)->unique();
            $table->decimal('total_salaries', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('total_pending', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_salary_batches');
    }
};
