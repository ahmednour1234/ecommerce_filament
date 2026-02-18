<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'birth_date')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('birth_date')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'birth_date')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->date('birth_date')->nullable()->change();
            });
        }
    }
};
