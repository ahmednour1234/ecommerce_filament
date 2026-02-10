<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN status ENUM(
            'new',
            'foreign_embassy_approval',
            'external_sending_office_approval',
            'accepted_by_external_sending_office',
            'foreign_labor_ministry_approval',
            'accepted_by_foreign_labor_ministry',
            'sent_to_saudi_embassy',
            'visa_issued',
            'arrived_in_saudi_arabia',
            'rejected',
            'cancelled',
            'visa_cancelled',
            'outside_kingdom',
            'processing',
            'contract_signed',
            'ticket_booked',
            'worker_received',
            'closed',
            'returned'
        ) DEFAULT 'new'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN status ENUM(
            'new',
            'processing',
            'contract_signed',
            'ticket_booked',
            'worker_received',
            'closed',
            'returned'
        ) DEFAULT 'new'");
    }
};
