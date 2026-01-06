<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_employee_group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('hr_employee_groups')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['group_id', 'employee_id']);
            $table->index('group_id');
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employee_group_members');
    }
};

