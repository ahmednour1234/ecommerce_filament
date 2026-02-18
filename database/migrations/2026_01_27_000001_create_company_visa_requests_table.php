<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('company_visa_requests')) {
            return;
        }

        Schema::create('company_visa_requests', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->date('request_date')->index();
            $table->unsignedBigInteger('profession_id')->nullable();
            $table->unsignedBigInteger('nationality_id')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->unsignedInteger('workers_count');
            $table->string('visa_number')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('remaining_count')->default(0);
            $table->enum('status', ['draft', 'paid', 'completed', 'rejected'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('profession_id')->references('id')->on('professions')->nullOnDelete();
            $table->foreign('nationality_id')->references('id')->on('nationalities')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_visa_requests');
    }
};
