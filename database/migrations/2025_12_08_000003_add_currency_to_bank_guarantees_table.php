<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bank_guarantees', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('amount')->constrained('currencies')->onDelete('set null');
            $table->decimal('exchange_rate', 10, 8)->default(1)->after('currency_id');
            $table->decimal('base_amount', 15, 2)->nullable()->after('exchange_rate');
            $table->decimal('base_bank_fees', 15, 2)->nullable()->after('bank_fees');

            $table->index('currency_id');
        });
    }

    public function down(): void
    {
        Schema::table('bank_guarantees', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id', 'exchange_rate', 'base_amount', 'base_bank_fees']);
        });
    }
};

