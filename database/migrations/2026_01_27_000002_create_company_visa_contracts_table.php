<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('company_visa_contracts');

        Schema::create('company_visa_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_no')->unique();
            $table->date('contract_date')->index();
            $table->unsignedBigInteger('visa_request_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('profession_id')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('workers_required');
            $table->unsignedInteger('linked_workers_count')->default(0);
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft')->index();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('visa_request_id')->references('id')->on('company_visa_requests')->cascadeOnDelete();
            $table->foreign('agent_id')->references('id')->on('agents')->nullOnDelete();
            $table->foreign('profession_id')->references('id')->on('professions')->nullOnDelete();
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_visa_contracts');
    }
};
