<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE complaints MODIFY COLUMN priority ENUM('very_high') DEFAULT 'very_high'");
            DB::statement("UPDATE complaints SET priority = 'very_high' WHERE priority NOT IN ('very_high')");
            
            DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM('in_progress', 'resolved') DEFAULT 'in_progress'");
            DB::statement("UPDATE complaints SET status = 'in_progress' WHERE status NOT IN ('in_progress', 'resolved')");
        } else {
            Schema::table('complaints', function (Blueprint $table) {
                $table->string('priority')->default('very_high')->change();
                $table->string('status')->default('in_progress')->change();
            });
            
            DB::table('complaints')->where('priority', '!=', 'very_high')->update(['priority' => 'very_high']);
            DB::table('complaints')->whereNotIn('status', ['in_progress', 'resolved'])->update(['status' => 'in_progress']);
        }
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            DB::statement("ALTER TABLE complaints MODIFY COLUMN priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium'");
            DB::statement("ALTER TABLE complaints MODIFY COLUMN status ENUM('pending', 'in_progress', 'resolved', 'closed') DEFAULT 'pending'");
        });
    }
};
