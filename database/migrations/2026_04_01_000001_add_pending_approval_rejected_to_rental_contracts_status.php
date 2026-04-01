<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE rental_contracts MODIFY COLUMN status ENUM('active','suspended','completed','cancelled','returned','archived','pending_approval','rejected') NOT NULL DEFAULT 'pending_approval'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE rental_contracts MODIFY COLUMN status ENUM('active','suspended','completed','cancelled','returned','archived') NOT NULL DEFAULT 'active'");
    }
};
