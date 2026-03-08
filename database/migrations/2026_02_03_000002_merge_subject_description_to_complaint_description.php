<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (Schema::hasColumn('complaints', 'subject') && Schema::hasColumn('complaints', 'description')) {
                DB::statement("UPDATE complaints SET description = CONCAT(COALESCE(subject, ''), '\n\n', COALESCE(description, '')) WHERE subject IS NOT NULL OR description IS NOT NULL");
                
                $table->dropColumn('subject');
            }
            
            if (!Schema::hasColumn('complaints', 'complaint_description')) {
                $table->text('complaint_description')->nullable()->after('nationality_id');
            }
            
            if (Schema::hasColumn('complaints', 'description')) {
                DB::statement("UPDATE complaints SET complaint_description = description WHERE complaint_description IS NULL");
                $table->dropColumn('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'subject')) {
                $table->string('subject')->after('nationality_id');
            }
            
            if (!Schema::hasColumn('complaints', 'description')) {
                $table->text('description')->after('subject');
            }
            
            if (Schema::hasColumn('complaints', 'complaint_description')) {
                DB::statement("UPDATE complaints SET description = complaint_description WHERE description IS NULL");
                $table->dropColumn('complaint_description');
            }
        });
    }
};
