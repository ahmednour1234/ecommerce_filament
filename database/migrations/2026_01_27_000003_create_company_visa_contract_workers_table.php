<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_visa_contract_workers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('worker_id');
            $table->decimal('cost_per_worker', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('contract_id')->references('id')->on('company_visa_contracts')->cascadeOnDelete();
            $table->foreign('worker_id')->references('id')->on('laborers')->cascadeOnDelete();
            $table->unique(['contract_id', 'worker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_visa_contract_workers');
    }
};
