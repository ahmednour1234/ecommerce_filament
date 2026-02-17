<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('service_transfers')) {
            return;
        }

        Schema::create('service_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique();
            $table->date('request_date');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('worker_id')->constrained('laborers')->onDelete('restrict');
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->foreignId('nationality_id')->constrained('nationalities')->onDelete('restrict');
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('external_cost', 12, 2)->default(0);
            $table->decimal('government_fees', 12, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(15);
            $table->decimal('tax_value', 12, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->string('discount_reason')->nullable();
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded'])->default('unpaid');
            $table->enum('request_status', ['active', 'archived', 'refunded'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('request_date');
            $table->index('branch_id');
            $table->index('payment_status');
            $table->index('request_status');
            $table->index('customer_id');
            $table->index('worker_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_transfers');
    }
};
