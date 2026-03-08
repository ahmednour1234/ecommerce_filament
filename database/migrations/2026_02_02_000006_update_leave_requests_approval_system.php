<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hr_leave_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'branch_manager_approved', 'approved', 'rejected', 'cancelled'])->default('pending')->change();
            
            $table->foreignId('branch_manager_approved_by')->nullable()->after('approved_by')->constrained('users')->nullOnDelete();
            $table->timestamp('branch_manager_approved_at')->nullable()->after('approved_at');
            
            $table->foreignId('general_manager_approved_by')->nullable()->after('branch_manager_approved_at')->constrained('users')->nullOnDelete();
            $table->timestamp('general_manager_approved_at')->nullable()->after('general_manager_approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('hr_leave_requests', function (Blueprint $table) {
            $table->dropForeign(['branch_manager_approved_by']);
            $table->dropForeign(['general_manager_approved_by']);
            $table->dropColumn(['branch_manager_approved_by', 'branch_manager_approved_at', 'general_manager_approved_by', 'general_manager_approved_at']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending')->change();
        });
    }
};
