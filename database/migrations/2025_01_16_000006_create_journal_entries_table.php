<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->onDelete('restrict');
            $table->string('entry_number')->unique();
            $table->date('entry_date');
            $table->string('reference')->nullable(); // External reference number
            $table->text('description')->nullable();
            $table->foreignId('branch_id')->constrained()->onDelete('restrict');
            $table->foreignId('cost_center_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // Created by
            $table->boolean('is_posted')->default(false);
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index('journal_id');
            $table->index('entry_date');
            $table->index('entry_number');
            $table->index('branch_id');
            $table->index('cost_center_id');
            $table->index('is_posted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};

