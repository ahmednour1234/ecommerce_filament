<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->enum('problem_type', [
                'salary_issue',
                'food_issue',
                'escape',
                'work_refusal'
            ])->nullable()->after('contract_id');
            
            $table->string('phone_number')->nullable()->after('problem_type');
            
            $table->foreignId('nationality_id')->nullable()->after('phone_number')
                ->constrained('nationalities')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['nationality_id']);
            $table->dropColumn(['problem_type', 'phone_number', 'nationality_id']);
        });
    }
};
