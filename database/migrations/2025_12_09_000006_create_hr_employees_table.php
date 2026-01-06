<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->string('fingerprint_device_id')->nullable();
            $table->string('profile_image')->nullable();
            $table->date('hire_date');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('department_id')->constrained('hr_departments')->onDelete('restrict');
            $table->foreignId('position_id')->constrained('hr_positions')->onDelete('restrict');
            $table->unsignedBigInteger('location_id')->nullable(); // Location table assumed to exist
            $table->decimal('basic_salary', 15, 2);
            $table->string('cv_file')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->foreignId('identity_type_id')->nullable()->constrained('hr_identity_types')->onDelete('set null');
            $table->string('identity_number')->nullable();
            $table->date('identity_expiry_date')->nullable();
            $table->foreignId('blood_type_id')->nullable()->constrained('hr_blood_types')->onDelete('set null');
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->foreignId('bank_id')->nullable()->constrained('hr_banks')->onDelete('set null');
            $table->string('bank_name_text')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('iban')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('employee_number');
            $table->index('branch_id');
            $table->index('department_id');
            $table->index('position_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employees');
    }
};

