<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rental_contract_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('desired_package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->unsignedInteger('desired_country_id')->nullable();
            $table->unsignedBigInteger('profession_id')->nullable();
            $table->enum('worker_gender', ['male', 'female'])->nullable();
            $table->date('start_date');
            $table->enum('duration_type', ['day', 'month', 'year']);
            $table->integer('duration');
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'converted'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('desired_country_id')->references('id')->on('countries')->nullOnDelete();
            $table->foreign('profession_id')->references('id')->on('professions')->nullOnDelete();

            $table->index('request_no');
            $table->index('customer_id');
            $table->index('branch_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_contract_requests');
    }
};
