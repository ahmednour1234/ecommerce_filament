<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('finance_types', function (Blueprint $table) {
            $table->id();
            $table->enum('kind', ['income', 'expense'])->index();
            $table->json('name');
            $table->string('code')->nullable();
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['kind', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_types');
    }
};
