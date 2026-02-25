<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('housing_salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('housing_salaries', 'laborer_id')) {
                $table->foreignId('laborer_id')->nullable()->after('employee_id')->constrained('laborers')->nullOnDelete();
                $table->index('laborer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('housing_salaries', function (Blueprint $table) {
            if (Schema::hasColumn('housing_salaries', 'laborer_id')) {
                $table->dropForeign(['laborer_id']);
                $table->dropIndex(['laborer_id']);
                $table->dropColumn('laborer_id');
            }
        });
    }
};
