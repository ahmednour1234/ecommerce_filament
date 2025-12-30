<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('account_id')->constrained('currencies')->onDelete('restrict');
            $table->decimal('exchange_rate', 18, 8)->default(1)->after('currency_id');
            $table->decimal('amount', 15, 2)->nullable()->after('exchange_rate');
            $table->decimal('base_amount', 15, 2)->nullable()->after('amount');
            $table->string('reference')->nullable()->after('description');
            $table->foreignId('project_id')->nullable()->after('cost_center_id')->constrained('projects')->onDelete('set null');
            
            $table->index('currency_id');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropForeign(['project_id']);
            $table->dropColumn(['currency_id', 'exchange_rate', 'amount', 'base_amount', 'reference', 'project_id']);
        });
    }
};

