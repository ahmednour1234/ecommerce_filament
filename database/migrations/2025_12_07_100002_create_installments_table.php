<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->morphs('installmentable'); // installmentable_type, installmentable_id (Order or Invoice)
            $table->string('installment_number')->unique();
            $table->decimal('total_amount', 18, 2);
            $table->integer('installment_count');
            $table->decimal('installment_amount', 18, 2);
            $table->date('start_date');
            $table->enum('frequency', ['daily', 'weekly', 'biweekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->string('status')->default('active'); // active, completed, cancelled, overdue
            $table->json('payment_schedule')->nullable(); // Pre-calculated payment dates
            $table->timestamps();

            $table->index('installment_number');
            $table->index('status');
            $table->index(['installmentable_type', 'installmentable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};

