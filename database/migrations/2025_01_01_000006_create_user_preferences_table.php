<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->foreignId('theme_id')->nullable()->constrained('themes')->nullOnDelete();
            $table->string('timezone')->nullable();        // Africa/Cairo
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
