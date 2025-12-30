<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // This migration adds foreign keys after all tables exist
        if (!Schema::hasColumn('vouchers', 'project_id')) {
            return;
        }
        
        Schema::table('vouchers', function (Blueprint $table) {
            // Add foreign keys if they don't exist
            try {
                if (Schema::hasTable('projects')) {
                    $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                }
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
            
            try {
                if (Schema::hasTable('fiscal_years')) {
                    $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('restrict');
                }
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
            
            try {
                if (Schema::hasTable('periods')) {
                    $table->foreign('period_id')->references('id')->on('periods')->onDelete('restrict');
                }
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
            
            try {
                $table->foreign('bg_parent_id')->references('id')->on('vouchers')->onDelete('set null');
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['fiscal_year_id']);
            $table->dropForeign(['period_id']);
        });
    }
};

