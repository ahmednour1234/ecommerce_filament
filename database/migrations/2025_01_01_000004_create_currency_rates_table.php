<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('base_currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->foreignId('target_currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->decimal('rate', 18, 8);
            $table->timestamp('valid_from')->nullable();
            $table->string('source')->nullable(); // fixer, exchangerate.host ...
            $table->timestamps();

            $table->unique(['base_currency_id','target_currency_id','valid_from'], 'currency_rate_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_rates');
    }
};
