<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entry_lines', 'currency_id')) {
                $table->unsignedBigInteger('currency_id')->nullable();
            }
            if (!Schema::hasColumn('journal_entry_lines', 'exchange_rate')) {
                $table->decimal('exchange_rate', 18, 8)->default(1);
            }
            if (!Schema::hasColumn('journal_entry_lines', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('journal_entry_lines', 'base_amount')) {
                $table->decimal('base_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('journal_entry_lines', 'reference')) {
                $table->string('reference')->nullable();
            }
            if (!Schema::hasColumn('journal_entry_lines', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable();
            }
        });
        
        // Add foreign keys if tables exist
        if (Schema::hasTable('currencies')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                try {
                    if (Schema::hasColumn('journal_entry_lines', 'currency_id')) {
                        $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('restrict');
                    }
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            });
        }
        
        if (Schema::hasTable('projects')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                try {
                    if (Schema::hasColumn('journal_entry_lines', 'project_id')) {
                        $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                    }
                } catch (\Exception $e) {
                    // Foreign key might already exist
                }
            });
        }
        
        // Add indexes
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            if (Schema::hasColumn('journal_entry_lines', 'currency_id')) {
                try {
                    $table->index('currency_id');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
            if (Schema::hasColumn('journal_entry_lines', 'project_id')) {
                try {
                    $table->index('project_id');
                } catch (\Exception $e) {
                    // Index might already exist
                }
            }
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

