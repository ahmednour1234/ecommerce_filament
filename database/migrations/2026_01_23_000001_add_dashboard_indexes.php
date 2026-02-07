<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('finance_branch_transactions', function (Blueprint $table) {
            if (!$this->hasIndex('finance_branch_transactions', 'idx_trx_date_branch_status')) {
                $table->index(['trx_date', 'branch_id', 'status'], 'idx_trx_date_branch_status');
            }
            if (!$this->hasIndex('finance_branch_transactions', 'idx_trx_date_branch_finance_type')) {
                $table->index(['trx_date', 'branch_id', 'finance_type_id'], 'idx_trx_date_branch_finance_type');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!$this->hasIndex('orders', 'idx_order_date_branch_status')) {
                $table->index(['order_date', 'branch_id', 'status'], 'idx_order_date_branch_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('finance_branch_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_trx_date_branch_status');
            $table->dropIndex('idx_trx_date_branch_finance_type');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_order_date_branch_status');
        });
    }

    protected function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        
        if ($connection->getDriverName() === 'sqlite') {
            $indexes = $connection->select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]);
            return !empty($indexes);
        }
        
        $indexes = $connection->select(
            "SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [$database, $table, $indexName]
        );
        
        return !empty($indexes);
    }
};
