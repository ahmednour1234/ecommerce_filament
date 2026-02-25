<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_commission_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->date('date_from');
            $table->date('date_to');
            $table->unsignedInteger('total_contracts')->default(0);
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->json('breakdown')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
            $table->index(['date_from', 'date_to']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_commission_runs');
    }
};
