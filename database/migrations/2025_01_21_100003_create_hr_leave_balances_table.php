<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained('hr_leave_types')->onDelete('cascade');
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('quota');
            $table->unsignedInteger('used')->default(0);
            $table->unsignedInteger('remaining');
            $table->timestamps();

            $table->unique(['employee_id', 'leave_type_id', 'year'], 'unique_employee_leave_year');
            $table->index('employee_id');
            $table->index('leave_type_id');
            $table->index('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_leave_balances');
    }
};

