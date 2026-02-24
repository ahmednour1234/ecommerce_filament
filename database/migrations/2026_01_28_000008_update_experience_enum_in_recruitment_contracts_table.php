<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN experience ENUM(
            'unspecified',
            'new',
            'ex_worker'
        ) NULL");
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->string('experience')->nullable()->change();
        });
    }
};
