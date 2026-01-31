<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rental_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_no')->unique();
            $table->string('request_no')->nullable();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('worker_id')->nullable()->constrained('laborers')->nullOnDelete();
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedBigInteger('profession_id')->nullable();
            $table->foreignId('package_id')->constrained('packages')->restrictOnDelete();
            $table->enum('status', ['active', 'suspended', 'completed', 'cancelled', 'returned', 'archived'])->default('active');
            $table->enum('payment_status', ['paid', 'unpaid', 'partial', 'refunded'])->default('unpaid');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('duration_type', ['day', 'month', 'year']);
            $table->integer('duration');
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->enum('discount_type', ['none', 'percent', 'fixed'])->default('none');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_value', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_total', 12, 2)->default(0);
            $table->decimal('remaining_total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
            $table->foreign('profession_id')->references('id')->on('professions')->nullOnDelete();

            $table->index('contract_no');
            $table->index('request_no');
            $table->index('branch_id');
            $table->index('customer_id');
            $table->index('worker_id');
            $table->index('package_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['start_date', 'end_date']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_contracts');
    }
};
