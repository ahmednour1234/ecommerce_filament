<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained('hr_work_schedules')->onDelete('cascade');
            $table->date('date_from');
            $table->date('date_to')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'date_from']);
            $table->index('employee_id');
            $table->index('schedule_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employee_schedules');
    }
};

