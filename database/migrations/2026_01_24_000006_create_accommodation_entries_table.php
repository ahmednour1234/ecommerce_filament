<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('accommodation_entries')) {
            return;
        }

        Schema::create('accommodation_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laborer_id')->constrained('laborers')->onDelete('cascade');
            $table->string('contract_no')->nullable();
            $table->enum('entry_type', ['new_arrival', 'return', 'transfer'])->default('new_arrival');
            $table->datetime('entry_date');
            $table->foreignId('building_id')->constrained('housing_buildings')->onDelete('restrict');
            $table->foreignId('status_id')->nullable()->constrained('housing_statuses')->onDelete('set null');
            $table->datetime('exit_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('laborer_id');
            $table->index('contract_no');
            $table->index('entry_type');
            $table->index('entry_date');
            $table->index('building_id');
            $table->index('status_id');
            $table->index('exit_date');
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_entries');
    }
};
