<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_employee_work_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->foreignId('work_place_id')->constrained('hr_work_places')->onDelete('cascade');
            $table->timestamps();

            $table->unique('employee_id');
            $table->index('employee_id');
            $table->index('work_place_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employee_work_places');
    }
};

