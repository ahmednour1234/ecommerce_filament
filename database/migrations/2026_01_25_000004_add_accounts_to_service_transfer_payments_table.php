<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_transfer_payments', function (Blueprint $table) {
            $table->foreignId('from_account_id')->nullable()->after('payment_method_id')->constrained('accounts')->onDelete('set null');
            $table->foreignId('to_account_id')->nullable()->after('from_account_id')->constrained('accounts')->onDelete('set null');
            $table->string('reference')->nullable()->after('to_account_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_transfer_payments', function (Blueprint $table) {
            $table->dropForeign(['from_account_id']);
            $table->dropForeign(['to_account_id']);
            $table->dropColumn(['from_account_id', 'to_account_id', 'reference']);
        });
    }
};
