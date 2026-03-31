<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN status ENUM(
            'new',
            'foreign_embassy_approval',
            'external_office_approval',
            'contract_accepted_external_office',
            'waiting_approval',
            'contract_accepted_labor_ministry',
            'sent_to_saudi_embassy',
            'visa_issued',
            'visa_cancelled',
            'travel_permit_after_visa_issued',
            'waiting_flight_booking',
            'arrival_scheduled',
            'received',
            'return_during_warranty',
            'runaway'
        ) DEFAULT 'new'");
    }

    public function down(): void
    {
        // Map visa_cancelled back to new before removing it
        DB::table('recruitment_contracts')
            ->where('status', 'visa_cancelled')
            ->update(['status' => 'new']);

        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN status ENUM(
            'new',
            'external_office_approval',
            'contract_accepted_external_office',
            'waiting_approval',
            'contract_accepted_labor_ministry',
            'sent_to_saudi_embassy',
            'visa_issued',
            'travel_permit_after_visa_issued',
            'waiting_flight_booking',
            'arrival_scheduled',
            'received',
            'return_during_warranty',
            'runaway'
        ) DEFAULT 'new'");
    }
};
