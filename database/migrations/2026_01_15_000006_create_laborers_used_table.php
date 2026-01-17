<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laborers_used', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laborer_id');
            $table->unsignedBigInteger('agent_id');
            $table->date('used_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('laborer_id')->references('id')->on('laborers')->onDelete('restrict');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('restrict');

            $table->index('laborer_id');
            $table->index('agent_id');
            $table->index('used_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laborers_used');
    }
};
