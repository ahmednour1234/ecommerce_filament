<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recruitment_contract_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruitment_contract_id')->constrained('recruitment_contracts')->cascadeOnDelete();
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('recruitment_contract_id');
            $table->index('new_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_contract_status_logs');
    }
};
