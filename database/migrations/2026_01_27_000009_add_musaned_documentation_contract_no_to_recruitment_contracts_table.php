<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->string('musaned_documentation_contract_no')->nullable()->after('musaned_contract_no');
        });
    }

    public function down(): void
    {
        Schema::table('recruitment_contracts', function (Blueprint $table) {
            $table->dropColumn('musaned_documentation_contract_no');
        });
    }
};
