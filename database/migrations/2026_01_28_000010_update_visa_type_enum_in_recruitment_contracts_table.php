<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("UPDATE recruitment_contracts SET visa_type = 'paid' WHERE visa_type IN ('qualification', 'additional', 'other')");
        
        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN visa_type ENUM('paid', 'domestic_labor', 'comprehensive_qualification') DEFAULT 'paid'");
    }

    public function down(): void
    {
        DB::statement("UPDATE recruitment_contracts SET visa_type = 'paid' WHERE visa_type IN ('domestic_labor', 'comprehensive_qualification')");
        
        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN visa_type ENUM('paid', 'qualification', 'additional') DEFAULT 'paid'");
    }
};
