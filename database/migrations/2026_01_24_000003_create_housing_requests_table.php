<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_requests')) {
            return;
        }

        Schema::create('housing_requests', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->string('contract_no')->nullable();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('laborer_id')->nullable()->constrained('laborers')->onDelete('set null');
            $table->enum('type', ['delivery', 'return'])->default('delivery');
            $table->date('request_date');
            $table->foreignId('status_id')->nullable()->constrained('housing_statuses')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('order_no');
            $table->index('contract_no');
            $table->index('client_id');
            $table->index('laborer_id');
            $table->index('type');
            $table->index('request_date');
            $table->index('status_id');
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_requests');
    }
};
