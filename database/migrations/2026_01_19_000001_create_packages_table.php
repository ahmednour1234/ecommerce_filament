<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('packages')) {
            return;
        }

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['recruitment', 'rental', 'service_transfer']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('duration_type', ['day', 'month', 'year']);
            $table->integer('duration');
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('external_costs', 12, 2)->default(0);
            $table->decimal('worker_salary', 12, 2)->default(0);
            $table->decimal('gov_fees', 12, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_value', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('status');
            $table->index('country_id');
            $table->index('created_by');

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
