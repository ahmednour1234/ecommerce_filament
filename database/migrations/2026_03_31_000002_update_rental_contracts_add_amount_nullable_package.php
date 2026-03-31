<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            // Add amount column (the base price the discount is calculated on)
            $table->decimal('amount', 12, 2)->default(0)->after('package_id');

            // Make package_id nullable (no longer required in the form)
            $table->dropForeign(['package_id']);
            $table->dropIndex(['package_id']);
            $table->unsignedBigInteger('package_id')->nullable()->change();
            $table->foreign('package_id')->references('id')->on('packages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rental_contracts', function (Blueprint $table) {
            $table->dropColumn('amount');

            $table->dropForeign(['package_id']);
            $table->unsignedBigInteger('package_id')->nullable(false)->change();
            $table->foreign('package_id')->references('id')->on('packages')->restrictOnDelete();
            $table->index('package_id');
        });
    }
};
