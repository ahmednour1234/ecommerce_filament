<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hr_departments', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->unsignedBigInteger('parent_id')->nullable()->after('slug');
            
            $table->foreign('parent_id')->references('id')->on('hr_departments')->onDelete('set null');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('hr_departments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id']);
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'parent_id']);
        });
    }
};
