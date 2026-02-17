<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('service_transfer_payments')) {
            return;
        }

        Schema::create('service_transfer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('service_transfers')->onDelete('cascade');
            $table->unsignedInteger('payment_no');
            $table->date('payment_date');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('restrict');
            $table->decimal('amount', 12, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('transfer_id');
            $table->index('payment_date');
            $table->index(['transfer_id', 'payment_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_transfer_payments');
    }
};
