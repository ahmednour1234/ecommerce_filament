<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_transfers', function (Blueprint $table) {
            $table->string('worker_passport_number')->nullable()->after('worker_id');
            $table->string('sponsorship_transfer_contract_image')->nullable()->after('worker_passport_number');
            $table->string('customer_id_number')->nullable()->after('customer_id');
            $table->string('customer_phone')->nullable()->after('customer_id_number');
            $table->string('customer_city')->nullable()->after('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('service_transfers', function (Blueprint $table) {
            $table->dropColumn([
                'worker_passport_number',
                'sponsorship_transfer_contract_image',
                'customer_id_number',
                'customer_phone',
                'customer_city'
            ]);
        });
    }
};
