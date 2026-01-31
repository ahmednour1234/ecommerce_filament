<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('package_details')) {
            return;
        }

        Schema::create('package_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->string('code');
            $table->string('title');
            $table->unsignedInteger('country_id')->nullable();
            $table->foreignId('profession_id')->nullable()->constrained('professions')->onDelete('set null');
            $table->decimal('direct_cost', 12, 2)->default(0);
            $table->decimal('gov_cost', 12, 2)->default(0);
            $table->decimal('external_cost', 12, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('tax_value', 12, 2)->default(0);
            $table->decimal('total_with_tax', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('package_id');
            $table->index('country_id');
            $table->index('profession_id');

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');

            $table->unique(['package_id', 'country_id', 'profession_id'], 'package_details_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_details');
    }
};
