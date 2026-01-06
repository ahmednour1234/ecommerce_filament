<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('hr_departments')->onDelete('set null');
            $table->index('department_id');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_positions');
    }
};

