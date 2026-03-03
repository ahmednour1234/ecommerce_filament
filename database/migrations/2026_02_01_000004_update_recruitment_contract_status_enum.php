<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN status ENUM(
            'new',
            'external_office_approval',
            'contract_accepted_external_office',
            'waiting_approval',
            'contract_accepted_labor_ministry',
            'sent_to_saudi_embassy',
            'visa_issued',
            'waiting_flight_booking',
            'arrival_scheduled',
            'received',
            'return_during_warranty',
            'runaway'
        ) DEFAULT 'new'");
    }

    public function down(): void
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
            'return_during_warranty',
            'outside_kingdom_during_warranty',
            'labor_services_transfer',
            'runaway',
            'temporary'
        ) DEFAULT 'new'");
    }
};
