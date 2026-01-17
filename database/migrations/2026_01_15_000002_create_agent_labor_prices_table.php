<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agent_labor_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->cascadeOnDelete();
            $table->unsignedBigInteger('nationality_id');
            $table->unsignedBigInteger('profession_id');
            $table->string('experience_level');
            $table->decimal('cost_amount', 12, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['agent_id', 'nationality_id', 'profession_id', 'experience_level', 'currency_id'], 'agent_labor_price_unique');
            $table->index('agent_id');
            $table->index('nationality_id');
            $table->index('profession_id');
            $table->index('currency_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_labor_prices');
    }
};
