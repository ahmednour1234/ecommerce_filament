<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('accommodation_entry_status_logs')) {
            return;
        }

        Schema::create('accommodation_entry_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_entry_id')->constrained('accommodation_entries')->onDelete('cascade');
            $table->foreignId('old_status_id')->nullable()->constrained('housing_statuses')->onDelete('set null');
            $table->foreignId('new_status_id')->nullable()->constrained('housing_statuses')->onDelete('set null');
            $table->date('status_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('accommodation_entry_id');
            $table->index('old_status_id');
            $table->index('new_status_id');
            $table->index('status_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_entry_status_logs');
    }
};
