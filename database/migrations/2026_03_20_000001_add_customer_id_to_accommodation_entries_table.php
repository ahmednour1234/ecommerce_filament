<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('accommodation_entries', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('customer_id_number');
                $table->foreign('customer_id')->references('id')->on('clients')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accommodation_entries', function (Blueprint $table) {
            if (Schema::hasColumn('accommodation_entries', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }
        });
    }
};
