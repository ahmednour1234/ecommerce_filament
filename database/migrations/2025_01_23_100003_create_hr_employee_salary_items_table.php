<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_employee_salary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('hr_employee_financial_profiles')->onDelete('cascade');
            $table->foreignId('component_id')->constrained('hr_salary_components')->onDelete('cascade');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['profile_id', 'component_id']);
            $table->index('profile_id');
            $table->index('component_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employee_salary_items');
    }
};
