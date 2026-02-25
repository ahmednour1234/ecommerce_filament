<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('housing_assignments')) {
            return;
        }

        Schema::create('housing_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laborer_id')->constrained('laborers')->onDelete('cascade');
            $table->foreignId('building_id')->constrained('housing_buildings')->onDelete('restrict');
            $table->foreignId('unit_id')->nullable()->constrained('housing_units')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('rent_amount', 12, 2)->default(0);
            $table->foreignId('status_id')->nullable()->constrained('housing_statuses')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('laborer_id');
            $table->index('building_id');
            $table->index('unit_id');
            $table->index('status_id');
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housing_assignments');
    }
};
