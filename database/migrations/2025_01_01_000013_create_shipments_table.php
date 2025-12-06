<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->morphs('shippable'); // shippable_type, shippable_id (مثلاً Order)
            $table->foreignId('shipping_provider_id')->constrained('shipping_providers')->cascadeOnDelete();
            $table->string('tracking_number')->nullable();
            $table->string('status')->default('created');
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->decimal('price', 18, 4)->nullable();
            $table->json('meta')->nullable(); // raw provider response
            $table->timestamps();

            $table->index(['status','shipping_provider_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
