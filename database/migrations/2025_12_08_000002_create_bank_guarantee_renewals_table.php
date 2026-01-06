<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_guarantee_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_guarantee_id')->constrained('bank_guarantees')->onDelete('cascade');
            $table->date('old_end_date');
            $table->date('new_end_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_at');

            $table->index('bank_guarantee_id');
            $table->index('new_end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_guarantee_renewals');
    }
};

