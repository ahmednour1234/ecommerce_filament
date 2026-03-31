<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rental_contract_requests', function (Blueprint $table) {
            $table->decimal('desired_amount', 12, 2)->default(0)->after('desired_package_id');
        });
    }

    public function down(): void
    {
        Schema::table('rental_contract_requests', function (Blueprint $table) {
            $table->dropColumn('desired_amount');
        });
    }
};
