<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('approval_logs')) {
            return;
        }
        
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('approvable'); // This already creates the index
            $table->enum('action', ['submitted', 'approved', 'rejected', 'posted'])->index();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Don't add index again - morphs() already creates it
            // $table->index(['approvable_type', 'approvable_id']);
            // $table->index('action'); // Already added above
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};

