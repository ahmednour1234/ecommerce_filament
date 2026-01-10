<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_loan_types', function (Blueprint $table) {
            $table->id();
            $table->json('name_json');
            $table->json('description_json')->nullable();
            $table->decimal('max_amount', 12, 2);
            $table->unsignedInteger('max_installments');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_loan_types');
    }
};
