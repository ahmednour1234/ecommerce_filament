<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_commission_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id')->constrained('commissions')->onDelete('cascade');
            $table->unsignedInteger('contracts_from');
            $table->unsignedInteger('contracts_to');
            $table->decimal('amount_per_contract', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['commission_id', 'contracts_from', 'contracts_to'], 'unique_commission_tier');
            $table->index('commission_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_commission_tiers');
    }
};
