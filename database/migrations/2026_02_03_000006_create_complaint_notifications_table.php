<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('complaint_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->enum('type', ['overdue', 'resolved', 'action_taken']);
            $table->text('message');
            $table->enum('status', ['unread', 'read'])->default('unread');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('complaint_id');
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_notifications');
    }
};
