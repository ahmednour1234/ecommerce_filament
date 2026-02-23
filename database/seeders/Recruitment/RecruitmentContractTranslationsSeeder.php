<?php

namespace Database\Seeders\Recruitment;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class RecruitmentContractTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping Recruitment Contract translations.');
            return;
        }

        $this->command->info('Creating Recruitment Contract translations...');

        $translations = [
            'navigation.recruitment_contracts' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'recruitment_contracts.title' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],

            'recruitment_contract.sections.basic_data' => ['en' => 'Basic Data', 'ar' => 'البيانات الأساسية'],
            'recruitment_contract.sections.additional_options' => ['en' => 'Additional Options', 'ar' => 'خيارات إضافية'],
            'recruitment_contract.sections.musaned_data' => ['en' => 'Musaned Data', 'ar' => 'بيانات مساند'],
            'recruitment_contract.sections.financial_data' => ['en' => 'Financial Data', 'ar' => 'البيانات المالية'],
            'recruitment_contract.sections.other_data' => ['en' => 'Other Data', 'ar' => 'البيانات الأخرى'],

            'recruitment_contract.fields.contract_no' => ['en' => 'Contract No', 'ar' => 'رقم العقد'],
            'recruitment_contract.fields.client' => ['en' => 'Client', 'ar' => 'العميل'],
            'recruitment_contract.fields.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'recruitment_contract.fields.gregorian_request_date' => ['en' => 'Gregorian Request Date', 'ar' => 'تاريخ الطلب (ميلادي)'],
            'recruitment_contract.fields.hijri_request_date' => ['en' => 'Hijri Request Date', 'ar' => 'تاريخ الطلب (هجري)'],
            'recruitment_contract.fields.visa_type' => ['en' => 'Visa Type', 'ar' => 'نوع التأشيرة'],
            'recruitment_contract.fields.visa_no' => ['en' => 'Visa No', 'ar' => 'رقم التأشيرة'],
            'recruitment_contract.fields.visa_date' => ['en' => 'Visa Date', 'ar' => 'تاريخ التأشيرة'],
            'recruitment_contract.fields.arrival_country' => ['en' => 'Arrival Country', 'ar' => 'محطة الوصول'],
            'recruitment_contract.fields.departure_country' => ['en' => 'Departure Country', 'ar' => 'محطة القدوم'],
            'recruitment_contract.fields.receiving_station' => ['en' => 'Receiving Station', 'ar' => 'محطة الاستلام'],
            'recruitment_contract.fields.profession' => ['en' => 'Profession', 'ar' => 'المهنة'],
            'recruitment_contract.fields.nationality' => ['en' => 'Nationality', 'ar' => 'الجنسية'],
            'recruitment_contract.fields.gender' => ['en' => 'Gender', 'ar' => 'الجنس'],
            'recruitment_contract.fields.experience' => ['en' => 'Experience', 'ar' => 'الخبرة'],
            'recruitment_contract.fields.religion' => ['en' => 'Religion', 'ar' => 'الدين'],
            'recruitment_contract.fields.workplace_ar' => ['en' => 'Workplace (Arabic)', 'ar' => 'مكان العمل (عربي)'],
            'recruitment_contract.fields.workplace_en' => ['en' => 'Workplace (English)', 'ar' => 'مكان العمل (إنجليزي)'],
            'recruitment_contract.fields.monthly_salary' => ['en' => 'Monthly Salary', 'ar' => 'الراتب الشهري'],
            'recruitment_contract.fields.musaned_contract_no' => ['en' => 'Musaned Contract No', 'ar' => 'رقم عقد مساند'],
            'recruitment_contract.fields.musaned_documentation_contract_no' => ['en' => 'Musaned Documentation Contract No', 'ar' => 'رقم عقد مساند توثيق'],
            'recruitment_contract.fields.musaned_auth_no' => ['en' => 'Musaned Auth No', 'ar' => 'رقم تفويض مساند'],
            'recruitment_contract.fields.musaned_contract_date' => ['en' => 'Musaned Contract Date', 'ar' => 'تاريخ عقد مساند'],
            'recruitment_contract.fields.direct_cost' => ['en' => 'Direct Cost', 'ar' => 'التكلفة المباشرة'],
            'recruitment_contract.fields.internal_ticket_cost' => ['en' => 'Internal Ticket Cost', 'ar' => 'تكلفة التذكرة الداخلية'],
            'recruitment_contract.fields.external_cost' => ['en' => 'External Cost', 'ar' => 'التكلفة الخارجية'],
            'recruitment_contract.fields.vat_cost' => ['en' => 'VAT Cost', 'ar' => 'تكلفة الضريبة'],
            'recruitment_contract.fields.gov_cost' => ['en' => 'Government Cost', 'ar' => 'التكلفة الحكومية'],
            'recruitment_contract.fields.total_cost' => ['en' => 'Total Cost', 'ar' => 'إجمالي التكلفة'],
            'recruitment_contract.fields.paid_total' => ['en' => 'Paid Total', 'ar' => 'المبلغ المدفوع'],
            'recruitment_contract.fields.remaining_total' => ['en' => 'Remaining Total', 'ar' => 'المبلغ المتبقي'],
            'recruitment_contract.fields.payment_status' => ['en' => 'Payment Status', 'ar' => 'حالة الدفع'],
            'recruitment_contract.fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'recruitment_contract.fields.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'recruitment_contract.fields.visa_image' => ['en' => 'Visa Image', 'ar' => 'صورة التأشيرة'],
            'recruitment_contract.fields.musaned_contract_file' => ['en' => 'Musaned Contract File', 'ar' => 'ملف عقد مساند'],
            'recruitment_contract.fields.worker' => ['en' => 'Worker', 'ar' => 'العامل'],
            'recruitment_contract.fields.amount' => ['en' => 'Amount', 'ar' => 'المبلغ'],
            'recruitment_contract.fields.payment_method' => ['en' => 'Payment Method', 'ar' => 'طريقة الدفع'],
            'recruitment_contract.fields.recipient_name' => ['en' => 'Recipient Name', 'ar' => 'اسم المستلم'],
            'recruitment_contract.fields.reference_no' => ['en' => 'Reference No', 'ar' => 'رقم المرجع'],
            'recruitment_contract.fields.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'recruitment_contract.fields.old_status' => ['en' => 'Old Status', 'ar' => 'الحالة السابقة'],
            'recruitment_contract.fields.new_status' => ['en' => 'New Status', 'ar' => 'الحالة الجديدة'],
            'recruitment_contract.fields.created_by' => ['en' => 'Created By', 'ar' => 'تم الإنشاء بواسطة'],

            'recruitment_contract.visa_type.paid' => ['en' => 'Paid', 'ar' => 'مدفوع'],
            'recruitment_contract.visa_type.qualification' => ['en' => 'Qualification', 'ar' => 'تأهيل'],
            'recruitment_contract.visa_type.additional' => ['en' => 'Additional Visa', 'ar' => 'تأشيرة إضافية'],

            'recruitment_contract.gender.male' => ['en' => 'Male', 'ar' => 'ذكر'],
            'recruitment_contract.gender.female' => ['en' => 'Female', 'ar' => 'أنثى'],

            'recruitment_contract.status.new' => ['en' => 'New', 'ar' => 'جديد'],
            'recruitment_contract.status.foreign_embassy_approval' => ['en' => 'Foreign Embassy Approval', 'ar' => 'موافقة السفارة الأجنبية'],
            'recruitment_contract.status.external_sending_office_approval' => ['en' => 'External Sending Office Approval', 'ar' => 'موافقة مكتب الإرسال الخارجيه'],
            'recruitment_contract.status.accepted_by_external_sending_office' => ['en' => 'Accepted by External Sending Office', 'ar' => 'تم القبول من مكتب الإرسال الخارجيه'],
            'recruitment_contract.status.foreign_labor_ministry_approval' => ['en' => 'Foreign Labor Ministry Approval', 'ar' => 'موافقة وزارة العمل الأجنبية'],
            'recruitment_contract.status.accepted_by_foreign_labor_ministry' => ['en' => 'Accepted by Foreign Labor Ministry', 'ar' => 'تم القبول من وزارة العمل الأجنبية'],
            'recruitment_contract.status.sent_to_saudi_embassy' => ['en' => 'Sent to Saudi Embassy', 'ar' => 'تم الإرسال للسفارة السعودية'],
            'recruitment_contract.status.visa_issued' => ['en' => 'Visa Issued', 'ar' => 'تم إصدار التأشيرة'],
            'recruitment_contract.status.arrived_in_saudi_arabia' => ['en' => 'Arrived in Saudi Arabia', 'ar' => 'وصل للمملكة العربية السعودية'],
            'recruitment_contract.status.return_during_warranty' => ['en' => 'Return During Warranty Period', 'ar' => 'رجيع في فتره الضمان'],
            'recruitment_contract.status.outside_kingdom_during_warranty' => ['en' => 'Outside Kingdom During Warranty Period', 'ar' => 'خارج المملكه في فتره الضمان'],
            'recruitment_contract.status.labor_services_transfer' => ['en' => 'Labor Services Transfer', 'ar' => 'نقل خدمات العماله'],
            'recruitment_contract.status.runaway' => ['en' => 'Runaway', 'ar' => 'هروب'],
            'recruitment_contract.status.rejected' => ['en' => 'Rejected', 'ar' => 'مرفوض'],
            'recruitment_contract.status.cancelled' => ['en' => 'Cancelled', 'ar' => 'ملغي'],
            'recruitment_contract.status.visa_cancelled' => ['en' => 'Visa Cancelled', 'ar' => 'إلغاء التأشيرة'],
            'recruitment_contract.status.outside_kingdom' => ['en' => 'Outside Kingdom', 'ar' => 'خارج المملكة'],
            'recruitment_contract.status.processing' => ['en' => 'Processing', 'ar' => 'قيد المعالجة'],
            'recruitment_contract.status.contract_signed' => ['en' => 'Contract Signed', 'ar' => 'تم توقيع العقد'],
            'recruitment_contract.status.ticket_booked' => ['en' => 'Ticket Booked', 'ar' => 'تم حجز التذكرة'],
            'recruitment_contract.status.worker_received' => ['en' => 'Worker Received', 'ar' => 'تم استلام العمالة'],
            'recruitment_contract.status.closed' => ['en' => 'Closed', 'ar' => 'مغلق'],
            'recruitment_contract.status.returned' => ['en' => 'Returned', 'ar' => 'مرتجع'],

            'recruitment_contract.payment_status.unpaid' => ['en' => 'Unpaid', 'ar' => 'غير مدفوع'],
            'recruitment_contract.payment_status.partial' => ['en' => 'Partial', 'ar' => 'جزئي'],
            'recruitment_contract.payment_status.paid' => ['en' => 'Paid', 'ar' => 'مدفوع'],

            'recruitment_contract.tabs.receipts' => ['en' => 'Receipts', 'ar' => 'سندات القبض'],
            'recruitment_contract.tabs.expenses' => ['en' => 'Expenses', 'ar' => 'مصروفات العقد'],
            'recruitment_contract.tabs.status_logs' => ['en' => 'Status Logs', 'ar' => 'سجل الأحداث'],

            'recruitment_contract.actions.create' => ['en' => 'Create Contract', 'ar' => 'إنشاء عقد'],
            'recruitment_contract.actions.download_template' => ['en' => 'Download Template', 'ar' => 'تحميل القالب'],
            'recruitment_contract.actions.import' => ['en' => 'Import Excel', 'ar' => 'استيراد Excel'],
            'recruitment_contract.actions.excel_file' => ['en' => 'Excel File', 'ar' => 'ملف Excel'],
            'recruitment_contract.actions.import_success' => ['en' => 'Recruitment contracts imported successfully', 'ar' => 'تم استيراد عقود الاستقدام بنجاح'],
            'recruitment_contract.actions.import_complete' => ['en' => 'Import Complete', 'ar' => 'اكتمل الاستيراد'],
            'recruitment_contract.actions.import_errors' => ['en' => 'Import Errors', 'ar' => 'أخطاء الاستيراد'],
            'recruitment_contract.actions.import_failed' => ['en' => 'Import Failed', 'ar' => 'فشل الاستيراد'],

            'recruitment_contract.dashboard.heading' => ['en' => 'Recruitment Contracts Statistics', 'ar' => 'إحصائيات عقود الاستقدام'],
            'recruitment_contract.dashboard.no_data' => ['en' => 'No Data', 'ar' => 'لا توجد بيانات'],
            'recruitment_contract.dashboard.no_contracts_period' => ['en' => 'No contracts in the selected period', 'ar' => 'لا توجد عقود في الفترة المحددة'],
            'recruitment_contract.dashboard.total_contracts' => ['en' => 'Total Contracts', 'ar' => 'إجمالي العقود'],
            'recruitment_contract.dashboard.in_period' => ['en' => 'In the selected period', 'ar' => 'في الفترة المحددة'],
            'recruitment_contract.dashboard.new_contracts' => ['en' => 'New Contracts', 'ar' => 'عقود جديدة'],
            'recruitment_contract.dashboard.processing_contracts' => ['en' => 'Processing Contracts', 'ar' => 'عقود قيد المعالجة'],
            'recruitment_contract.dashboard.visa_issued' => ['en' => 'Visa Issued', 'ar' => 'تم إصدار التأشيرة'],
            'recruitment_contract.dashboard.arrived' => ['en' => 'Arrived', 'ar' => 'وصل للمملكة'],
            'recruitment_contract.dashboard.closed_contracts' => ['en' => 'Closed Contracts', 'ar' => 'عقود مغلقة'],
            'recruitment_contract.dashboard.rejected_contracts' => ['en' => 'Rejected Contracts', 'ar' => 'عقود مرفوضة'],
            'recruitment_contract.dashboard.all_contracts' => ['en' => 'All Contracts', 'ar' => 'جميع العقود'],
            'recruitment_contract.dashboard.remaining' => ['en' => 'Remaining', 'ar' => 'المتبقي'],

            'sidebar.عقود_الاستقدام' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'sidebar.عقود_الاستقدام.recruitment_contract' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'recruitment_contract.menu.list' => ['en' => 'Recruitment Contracts', 'ar' => 'عقود الاستقدام'],
            'recruitment_contract.menu.add_new' => ['en' => 'Add New Contract', 'ar' => 'إضافة عقد جديد'],
            'recruitment_contract.menu.received_workers' => ['en' => 'Received Workers', 'ar' => 'العمالة المستلمة'],
            'recruitment_contract.menu.expired_contracts' => ['en' => 'Expired Contracts', 'ar' => 'العقود المنتهية'],
            'recruitment_contract.menu.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],

            'recruitment_contract.stats.new' => ['en' => 'New Contracts', 'ar' => 'عقود جديدة'],
            'recruitment_contract.stats.expired' => ['en' => 'Expired Contracts', 'ar' => 'العقود المنتهية'],
            'recruitment_contract.stats.returned' => ['en' => 'Returned/Cancelled Contracts', 'ar' => 'عقود مسترجعة'],
            'recruitment_contract.stats.warranty' => ['en' => 'Contracts in Warranty Period', 'ar' => 'عقود بفترة الضمان'],
            'recruitment_contract.stats.rejected' => ['en' => 'Rejected Contracts', 'ar' => 'عقود مرفوضة'],
            'recruitment_contract.stats.signed' => ['en' => 'Signed Contracts', 'ar' => 'عقود تم توقيع العقد'],
            'recruitment_contract.stats.visa_issued' => ['en' => 'Visa Issued Contracts', 'ar' => 'عقود تم إصدار تأشيراتها'],
            'recruitment_contract.stats.arrival_ticket_issued' => ['en' => 'Arrival Ticket Issued Contracts', 'ar' => 'عقود تم إصدار تذاكر الوصول'],

            'recruitment_contract.reports.total_contracts' => ['en' => 'Total Contracts', 'ar' => 'إجمالي العقود'],
            'recruitment_contract.reports.total_cost' => ['en' => 'Total Cost', 'ar' => 'إجمالي التكلفة'],
            'recruitment_contract.reports.paid_total' => ['en' => 'Paid Total', 'ar' => 'المبلغ المدفوع'],
            'recruitment_contract.reports.remaining_total' => ['en' => 'Remaining Total', 'ar' => 'المبلغ المتبقي'],
            'recruitment_contract.reports.received_workers' => ['en' => 'Received Workers', 'ar' => 'العمالة المستلمة'],
            'recruitment_contract.reports.closed_contracts' => ['en' => 'Closed Contracts', 'ar' => 'العقود المغلقة'],

            'common.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'common.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'common.view_all' => ['en' => 'View All', 'ar' => 'عرض الكل'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
            $resultEn = Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => 'dashboard',
                    'language_id' => $english->id,
                ],
                [
                    'value' => $values['en'],
                ]
            );

            $resultAr = Translation::updateOrCreate(
                [
                    'key' => $key,
                    'group' => 'dashboard',
                    'language_id' => $arabic->id,
                ],
                [
                    'value' => $values['ar'],
                ]
            );

            if ($resultEn->wasRecentlyCreated || $resultAr->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✓ Recruitment Contract translations created: {$created}, updated: {$updated}");
    }
}
