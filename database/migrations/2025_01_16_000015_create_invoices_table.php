<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('branch_id')->constrained()->onDelete('restrict');
            $table->foreignId('cost_center_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('draft'); // draft, sent, paid, overdue, cancelled
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->foreignId('currency_id')->nullable()->constrained()->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index('invoice_number');
            $table->index('invoice_date');
            $table->index('customer_id');
            $table->index('branch_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

