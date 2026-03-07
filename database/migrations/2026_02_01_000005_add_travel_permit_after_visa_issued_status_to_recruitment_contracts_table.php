<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN status VARCHAR(255) DEFAULT 'new'");

        $validStatuses = [
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
        ];

        DB::table('recruitment_contracts')
            ->whereNotIn('status', $validStatuses)
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

    public function down(): void
    {
        DB::statement("ALTER TABLE recruitment_contracts MODIFY COLUMN status VARCHAR(255) DEFAULT 'new'");

        DB::table('recruitment_contracts')
            ->where('status', 'travel_permit_after_visa_issued')
            ->update(['status' => 'visa_issued']);

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
};
