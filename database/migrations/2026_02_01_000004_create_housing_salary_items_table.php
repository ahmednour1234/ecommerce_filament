<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_salary_items')) {
            return;
        }

        Schema::create('housing_salary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('housing_salary_batches')->onDelete('cascade');
            $table->foreignId('laborer_id')->constrained('laborers')->onDelete('cascade');
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('deductions_total', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->enum('status', ['paid', 'pending'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index('batch_id');
            $table->index('laborer_id');
            $table->index('status');
            $table->unique(['batch_id', 'laborer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_salary_items');
    }
};
