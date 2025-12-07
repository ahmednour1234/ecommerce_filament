<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->enum('type', ['payment', 'receipt']);
            $table->date('voucher_date');
            $table->decimal('amount', 15, 2);
            $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
            $table->foreignId('journal_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('branch_id')->constrained()->onDelete('restrict');
            $table->foreignId('cost_center_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index('voucher_number');
            $table->index('type');
            $table->index('voucher_date');
            $table->index('branch_id');
            $table->index('cost_center_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};

