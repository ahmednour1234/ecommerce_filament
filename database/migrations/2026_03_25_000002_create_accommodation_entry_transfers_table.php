<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodation_entry_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_entry_id')
                  ->constrained('accommodation_entries')
                  ->cascadeOnDelete();
            $table->foreignId('transfer_client_id')
                  ->nullable()
                  ->constrained('clients')
                  ->nullOnDelete();
            $table->string('contract_file_path')->nullable();
            $table->string('contract_file_name')->nullable();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_entry_transfers');
    }
};
