<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index(); // Translation key (e.g., 'dashboard.welcome')
            $table->string('group')->default('dashboard')->index(); // Group like 'dashboard', 'auth', etc.
            $table->foreignId('language_id')->constrained('languages')->onDelete('cascade');
            $table->text('value'); // Translated text
            $table->timestamps();

            // Ensure unique translation per language
            $table->unique(['key', 'language_id', 'group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};

