<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE laborers MODIFY COLUMN agent_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE laborers MODIFY COLUMN agent_id BIGINT UNSIGNED NOT NULL');
    }
};
