<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (!Schema::hasColumn('complaints', 'complaint_description')) {
                $table->text('complaint_description')->nullable()->after('nationality_id');
            }
        });

        if (Schema::hasColumn('complaints', 'subject') && Schema::hasColumn('complaints', 'description')) {
            DB::statement("UPDATE complaints SET complaint_description = CONCAT(COALESCE(subject, ''), IF(COALESCE(description, '') != '', CONCAT('\n\n', description), '')) WHERE complaint_description IS NULL");
        } elseif (Schema::hasColumn('complaints', 'description')) {
            DB::statement("UPDATE complaints SET complaint_description = description WHERE complaint_description IS NULL");
        } elseif (Schema::hasColumn('complaints', 'subject')) {
            DB::statement("UPDATE complaints SET complaint_description = subject WHERE complaint_description IS NULL");
        }

        Schema::table('complaints', function (Blueprint $table) {
            if (Schema::hasColumn('complaints', 'subject')) {
                $table->dropColumn('subject');
            }
            
            if (Schema::hasColumn('complaints', 'description')) {
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
