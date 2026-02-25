<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_units')) {
            return;
        }

        Schema::create('housing_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained('housing_buildings')->onDelete('cascade');
            $table->string('unit_number');
            $table->integer('floor')->nullable();
            $table->integer('capacity')->default(1);
            $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('building_id');
            $table->index('status');
            $table->unique(['building_id', 'unit_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_units');
    }
};
