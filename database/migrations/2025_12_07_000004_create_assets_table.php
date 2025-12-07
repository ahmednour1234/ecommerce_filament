<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('cost_center_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['fixed', 'current', 'intangible', 'investment'])->default('fixed');
            $table->enum('category', ['property', 'equipment', 'vehicle', 'furniture', 'computer', 'other'])->default('other');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 15, 2)->default(0);
            $table->decimal('current_value', 15, 2)->default(0);
            $table->decimal('depreciation_rate', 5, 2)->default(0);
            $table->integer('useful_life_years')->nullable();
            $table->string('location')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'deprecated', 'disposed', 'maintenance'])->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('account_id');
            $table->index('branch_id');
            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};

