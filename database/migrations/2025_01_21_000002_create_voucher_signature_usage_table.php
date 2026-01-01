<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('voucher_signature_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
            $table->foreignId('signature_id')->constrained('voucher_signatures')->onDelete('cascade');
            $table->integer('position'); // Order 1..N
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('voucher_id');
            $table->index('signature_id');
            $table->index('position');
            $table->unique(['voucher_id', 'position']); // Ensure unique position per voucher
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_signature_usage');
    }
};

