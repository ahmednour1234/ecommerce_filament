<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaint_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->timestamp('status_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('complaint_id');
            $table->index('status_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_status_logs');
    }
};
