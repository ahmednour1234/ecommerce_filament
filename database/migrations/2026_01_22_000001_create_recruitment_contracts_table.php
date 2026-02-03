<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recruitment_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_no')->unique();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->date('gregorian_request_date')->default(now());
            $table->string('hijri_request_date')->nullable();
            $table->enum('visa_type', ['paid', 'qualification', 'other'])->default('paid');
            $table->string('visa_no');
            $table->date('visa_date')->nullable();
            $table->unsignedInteger('arrival_country_id');
            $table->unsignedInteger('departure_country_id');
            $table->unsignedBigInteger('profession_id')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('experience')->nullable();
            $table->string('religion')->nullable();
            $table->string('workplace_ar')->nullable();
            $table->string('workplace_en')->nullable();
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->string('musaned_contract_no')->nullable();
            $table->string('musaned_auth_no')->nullable();
            $table->date('musaned_contract_date')->nullable();
            $table->decimal('direct_cost', 12, 2)->default(0);
            $table->decimal('internal_ticket_cost', 12, 2)->default(0);
            $table->decimal('external_cost', 12, 2)->default(0);
            $table->decimal('vat_cost', 12, 2)->default(0);
            $table->decimal('gov_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->decimal('paid_total', 12, 2)->default(0);
            $table->decimal('remaining_total', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('status', ['new', 'processing', 'contract_signed', 'ticket_booked', 'worker_received', 'closed', 'returned'])->default('new');
            $table->text('notes')->nullable();
            $table->string('visa_image')->nullable();
            $table->string('musaned_contract_file')->nullable();
            $table->foreignId('worker_id')->nullable()->constrained('laborers')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('arrival_country_id')->references('id')->on('countries')->restrictOnDelete();
            $table->foreign('departure_country_id')->references('id')->on('countries')->restrictOnDelete();
            $table->foreign('profession_id')->references('id')->on('professions')->nullOnDelete();

            $table->index('contract_no');
            $table->index('client_id');
            $table->index('branch_id');
            $table->index('arrival_country_id');
            $table->index('departure_country_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_contracts');
    }
};
