<?php

namespace Database\Seeders\Housing;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class HousingTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Housing translations...');

        $arabic = Language::where('code', 'ar')->first();
        $english = Language::where('code', 'en')->first();

        if (!$arabic || !$english) {
            $this->command->error('Languages not found. Please run LanguageSeeder first.');
            return;
        }

        $translations = [
            // Navigation
            'sidebar.housing.dashboard' => ['ar' => 'لوحة تحكم قسم الإيواء', 'en' => 'Housing Dashboard'],
            'sidebar.housing.workers' => ['ar' => 'العمالة', 'en' => 'Workers'],
            'sidebar.housing.available_workers' => ['ar' => 'العمالة المتاحة', 'en' => 'Available Workers'],
            'sidebar.housing.requests' => ['ar' => 'طلبات الإيواء', 'en' => 'Housing Requests'],
            'sidebar.housing.salary_batches' => ['ar' => 'رواتب العمالة', 'en' => 'Worker Salaries'],
            'sidebar.housing.salary_deductions' => ['ar' => 'خصومات العمالة', 'en' => 'Salary Deductions'],
            'sidebar.housing.leaves' => ['ar' => 'إجازات العمالة', 'en' => 'Worker Leaves'],
            'sidebar.housing.drivers' => ['ar' => 'إدارة السائقين', 'en' => 'Drivers Management'],
            'sidebar.housing.reports' => ['ar' => 'التقارير', 'en' => 'Reports'],

            // Dashboard
            'housing.dashboard.heading' => ['ar' => 'لوحة تحكم قسم الإيواء', 'en' => 'Housing Dashboard'],
            'housing.dashboard.completed_requests' => ['ar' => 'طلبات مكتملة', 'en' => 'Completed Requests'],
            'housing.dashboard.approved_requests' => ['ar' => 'طلبات موافق عليها', 'en' => 'Approved Requests'],
            'housing.dashboard.pending_requests' => ['ar' => 'طلبات معلقة', 'en' => 'Pending Requests'],
            'housing.dashboard.request_type' => ['ar' => 'نوع الطلب', 'en' => 'Request Type'],
            'housing.dashboard.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'housing.dashboard.from_date' => ['ar' => 'من تاريخ', 'en' => 'From Date'],
            'housing.dashboard.to_date' => ['ar' => 'إلى تاريخ', 'en' => 'To Date'],

            // Requests
            'housing.requests.type.new_rent' => ['ar' => 'إيجار جديد', 'en' => 'New Rent'],
            'housing.requests.type.cancel_rent' => ['ar' => 'إلغاء الإيجار', 'en' => 'Cancel Rent'],
            'housing.requests.type.transfer_kafala' => ['ar' => 'نقل الكفالة', 'en' => 'Transfer Kafala'],
            'housing.requests.type.outside_service' => ['ar' => 'خارج الخدمة', 'en' => 'Outside Service'],
            'housing.requests.type.leave_request' => ['ar' => 'طلب إجازة', 'en' => 'Leave Request'],
            'housing.requests.status.pending' => ['ar' => 'معلق', 'en' => 'Pending'],
            'housing.requests.status.approved' => ['ar' => 'موافق عليه', 'en' => 'Approved'],
            'housing.requests.status.completed' => ['ar' => 'مكتمل', 'en' => 'Completed'],
            'housing.requests.status.rejected' => ['ar' => 'مرفوض', 'en' => 'Rejected'],
            'housing.requests.status.suspended' => ['ar' => 'موقوف', 'en' => 'Suspended'],

            // Actions
            'actions.housing.approve' => ['ar' => 'موافقة', 'en' => 'Approve'],
            'actions.housing.reject' => ['ar' => 'رفض', 'en' => 'Reject'],
            'actions.housing.complete' => ['ar' => 'إكمال', 'en' => 'Complete'],
            'actions.housing.suspend' => ['ar' => 'تعليق', 'en' => 'Suspend'],
            'actions.housing.register_return' => ['ar' => 'تسجيل العودة', 'en' => 'Register Return'],
            'actions.housing.add_leave' => ['ar' => 'إضافة إجازة جديدة', 'en' => 'Add New Leave'],
            'actions.housing.complete_ended_leaves' => ['ar' => 'إكمال الإجازات المنتهية', 'en' => 'Complete Ended Leaves'],
            'actions.housing.generate_salaries' => ['ar' => 'إنشاء رواتب لجميع العمالة', 'en' => 'Generate Salaries for All Workers'],
            'actions.housing.view_details' => ['ar' => 'عرض التفاصيل', 'en' => 'View Details'],
            'actions.print' => ['ar' => 'طباعة', 'en' => 'Print'],

            // Tables
            'tables.housing.workers.id' => ['ar' => 'رقم', 'en' => 'ID'],
            'tables.housing.workers.name' => ['ar' => 'الاسم', 'en' => 'Name'],
            'tables.housing.workers.nationality' => ['ar' => 'الدولة', 'en' => 'Nationality'],
            'tables.housing.workers.passport' => ['ar' => 'رقم الجواز', 'en' => 'Passport'],
            'tables.housing.workers.profession' => ['ar' => 'المهنة', 'en' => 'Profession'],
            'tables.housing.workers.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'tables.housing.workers.branch' => ['ar' => 'الفرع', 'en' => 'Branch'],
            'tables.housing.workers.rating' => ['ar' => 'التقييم', 'en' => 'Rating'],

            // Forms
            'forms.housing.deduction.laborer' => ['ar' => 'اسم العامل', 'en' => 'Worker Name'],
            'forms.housing.deduction.date' => ['ar' => 'تاريخ الخصم', 'en' => 'Deduction Date'],
            'forms.housing.deduction.type' => ['ar' => 'نوع الخصم', 'en' => 'Deduction Type'],
            'forms.housing.deduction.amount' => ['ar' => 'المبلغ', 'en' => 'Amount'],
            'forms.housing.deduction.reason' => ['ar' => 'سبب الخصم', 'en' => 'Reason'],
            'forms.housing.deduction.notes' => ['ar' => 'ملاحظات', 'en' => 'Notes'],
            'forms.housing.deduction.status' => ['ar' => 'الحالة', 'en' => 'Status'],
        ];

        $created = 0;
        foreach ($translations as $key => $values) {
            foreach (['ar', 'en'] as $lang) {
                $language = $lang === 'ar' ? $arabic : $english;
                $value = $values[$lang] ?? $values['en'];

                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $language->id,
                    ],
                    [
                        'value' => $value,
                    ]
                );
                $created++;
            }
        }

        $this->command->info("✓ Created {$created} housing translations");
    }
}
