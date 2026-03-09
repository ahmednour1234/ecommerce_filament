<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $arabicLanguage = DB::table('languages')->where('code', 'ar')->first();
        $englishLanguage = DB::table('languages')->where('code', 'en')->first();

        if (!$arabicLanguage || !$englishLanguage) {
            return;
        }

        $translations = [
            'recruitment_contract.status.external_office_approval' => ['en' => 'External Office Approval', 'ar' => 'بانتظار موافقه المكتب الخارجي'],
            'recruitment_contract.status.contract_accepted_external_office' => ['en' => 'Contract Accepted by External Office', 'ar' => 'تم قبول مكتب المكتب الخارجي'],
            'recruitment_contract.status.waiting_approval' => ['en' => 'Waiting Approval', 'ar' => 'انتظار الابروف'],
            'recruitment_contract.status.contract_accepted_labor_ministry' => ['en' => 'Contract Accepted by Labor Ministry', 'ar' => 'قبول العقد من مكتب العمل الخارجي'],
            'recruitment_contract.status.sent_to_saudi_embassy' => ['en' => 'Sent to Saudi Embassy', 'ar' => 'إرسال التأشيرة إلى السفارة السعودية'],
            'recruitment_contract.status.visa_issued' => ['en' => 'Visa Issued', 'ar' => 'تم التفييز'],
            'recruitment_contract.status.waiting_flight_booking' => ['en' => 'Waiting Flight Booking', 'ar' => 'انتظار حجز تذكرة الطيران'],
            'recruitment_contract.status.arrival_scheduled' => ['en' => 'Arrival Scheduled', 'ar' => 'معاد الوصول'],
            'recruitment_contract.status.received' => ['en' => 'Received', 'ar' => 'تم الاستلام'],
            'recruitment_contract.status.return_during_warranty' => ['en' => 'Return During Warranty Period', 'ar' => 'رجيع خلال فترة الضمان'],
            'recruitment_contract.status.runaway' => ['en' => 'Runaway', 'ar' => 'هروب'],
        ];

        foreach ($translations as $key => $values) {
            // Update or create Arabic translation
            DB::table('translations')->updateOrInsert(
                [
                    'key' => $key,
                    'group' => 'dashboard',
                    'language_id' => $arabicLanguage->id,
                ],
                [
                    'value' => $values['ar'],
                ]
            );

            // Update or create English translation
            DB::table('translations')->updateOrInsert(
                [
                    'key' => $key,
                    'group' => 'dashboard',
                    'language_id' => $englishLanguage->id,
                ],
                [
                    'value' => $values['en'],
                ]
            );
        }
    }

    public function down(): void
    {
        // Optionally revert translations if needed
    }
};
