<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->enum('category', ['fixed', 'intangible', 'current', 'investment'])->default('fixed');
            $table->decimal('purchase_cost', 18, 2);
            $table->decimal('current_value', 18, 2)->nullable();
            $table->date('purchase_date');
            $table->date('warranty_expiry_date')->nullable();
            $table->integer('useful_life_years')->nullable();
            $table->decimal('depreciation_rate', 5, 2)->nullable();
            $table->string('location')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('status')->default('active'); // active, disposed, sold, damaged
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('asset_number');
            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

