<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN visa_type ENUM('paid', 'qualification', 'additional') DEFAULT 'paid'");

            DB::statement("UPDATE recruitment_contracts SET visa_type = 'additional' WHERE visa_type = 'other'");
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            DB::statement("UPDATE recruitment_contracts SET visa_type = 'other' WHERE visa_type = 'additional'");

            DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN visa_type ENUM('paid', 'qualification', 'other') DEFAULT 'paid'");
        });
    }
};
