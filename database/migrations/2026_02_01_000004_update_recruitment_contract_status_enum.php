<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // First, update existing data to map old statuses to new ones
        $statusMapping = [
            'foreign_embassy_approval' => 'external_office_approval',
            'external_sending_office_approval' => 'external_office_approval',
            'accepted_by_external_sending_office' => 'contract_accepted_external_office',
            'foreign_labor_ministry_approval' => 'waiting_approval',
            'accepted_by_foreign_labor_ministry' => 'contract_accepted_labor_ministry',
            'arrived_in_saudi_arabia' => 'received',
            'worker_received' => 'received',
            'ticket_booked' => 'waiting_flight_booking',
            'contract_signed' => 'contract_accepted_external_office',
            'processing' => 'external_office_approval',
            'closed' => 'received',
            'returned' => 'return_during_warranty',
            'outside_kingdom_during_warranty' => 'return_during_warranty',
            'labor_services_transfer' => 'received',
            'temporary' => 'new',
            'rejected' => 'new',
            'cancelled' => 'new',
            'visa_cancelled' => 'new',
            'outside_kingdom' => 'return_during_warranty',
        ];

        foreach ($statusMapping as $oldStatus => $newStatus) {
            DB::table('recruitment_contracts')
                ->where('status', $oldStatus)
                ->update(['status' => $newStatus]);
        }

        // Now update the enum
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
        // Map new statuses back to old ones (approximate mapping)
        $reverseMapping = [
            'external_office_approval' => 'external_sending_office_approval',
            'contract_accepted_external_office' => 'accepted_by_external_sending_office',
            'waiting_approval' => 'foreign_labor_ministry_approval',
            'contract_accepted_labor_ministry' => 'accepted_by_foreign_labor_ministry',
            'waiting_flight_booking' => 'ticket_booked',
            'arrival_scheduled' => 'ticket_booked',
            'received' => 'arrived_in_saudi_arabia',
        ];

        foreach ($reverseMapping as $newStatus => $oldStatus) {
            DB::table('recruitment_contracts')
                ->where('status', $newStatus)
                ->update(['status' => $oldStatus]);
        }

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
