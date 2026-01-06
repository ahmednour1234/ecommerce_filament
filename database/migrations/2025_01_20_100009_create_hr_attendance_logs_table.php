<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('hr_employees')->onDelete('cascade');
            $table->dateTime('log_datetime');
            $table->string('type'); // check_in, check_out, or device_code
            $table->enum('source', ['manual', 'device', 'api'])->default('manual');
            $table->foreignId('device_id')->nullable()->constrained('hr_devices')->nullOnDelete();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'log_datetime']);
            $table->index('employee_id');
            $table->index('log_datetime');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_attendance_logs');
    }
};

