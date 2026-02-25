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
            'sidebar.housing_department' => ['ar' => 'قسم الإيواء', 'en' => 'Housing Department'],
            'sidebar.housing.dashboard' => ['ar' => 'لوحة تحكم قسم الإيواء', 'en' => 'Housing Dashboard'],
            'sidebar.housing.workers' => ['ar' => 'العمالة', 'en' => 'Workers'],
            'sidebar.housing.available_workers' => ['ar' => 'العمالة المتاحة', 'en' => 'Available Workers'],
            'sidebar.housing.requests' => ['ar' => 'طلبات الإيواء', 'en' => 'Housing Requests'],
            'sidebar.housing.salary_batches' => ['ar' => 'رواتب العمالة', 'en' => 'Worker Salaries'],
            'sidebar.housing.salary_deductions' => ['ar' => 'خصومات العمالة', 'en' => 'Salary Deductions'],
            'sidebar.housing.leaves' => ['ar' => 'إجازات العمالة', 'en' => 'Worker Leaves'],
            'sidebar.housing.drivers' => ['ar' => 'إدارة السائقين', 'en' => 'Drivers Management'],
            'sidebar.housing.reports' => ['ar' => 'التقارير', 'en' => 'Reports'],

            // Recruitment Housing Navigation
            'sidebar.housing.recruitment_housing' => ['ar' => 'إيواء الاستقدام', 'en' => 'Recruitment Housing'],
            'sidebar.housing.recruitment_housing.dashboard' => ['ar' => 'لوحة تحكم قسم الإيواء', 'en' => 'Housing Dashboard'],
            'sidebar.housing.recruitment_housing.workers' => ['ar' => 'العمالة', 'en' => 'Workers'],
            'sidebar.housing.recruitment_housing.available_workers' => ['ar' => 'العمالة المتاحة', 'en' => 'Available Workers'],
            'sidebar.housing.recruitment_housing.requests' => ['ar' => 'طلبات الإيواء', 'en' => 'Housing Requests'],
            'sidebar.housing.recruitment_housing.salary_batches' => ['ar' => 'رواتب العمالة', 'en' => 'Worker Salaries'],
            'sidebar.housing.recruitment_housing.salary_deductions' => ['ar' => 'خصومات العمالة', 'en' => 'Salary Deductions'],
            'sidebar.housing.recruitment_housing.leaves' => ['ar' => 'إجازات العمالة', 'en' => 'Worker Leaves'],
            'sidebar.housing.recruitment_housing.reports' => ['ar' => 'التقارير', 'en' => 'Reports'],

            // Rental Housing Navigation
            'sidebar.housing.rental_housing' => ['ar' => 'إيواء التأجير', 'en' => 'Rental Housing'],
            'sidebar.housing.rental_housing.dashboard' => ['ar' => 'لوحة تحكم قسم الإيواء', 'en' => 'Housing Dashboard'],
            'sidebar.housing.rental_housing.workers' => ['ar' => 'العمالة', 'en' => 'Workers'],
            'sidebar.housing.rental_housing.available_workers' => ['ar' => 'العمالة المتاحة', 'en' => 'Available Workers'],
            'sidebar.housing.rental_housing.requests' => ['ar' => 'طلبات الإيواء', 'en' => 'Housing Requests'],
            'sidebar.housing.rental_housing.salary_batches' => ['ar' => 'رواتب العمالة', 'en' => 'Worker Salaries'],
            'sidebar.housing.rental_housing.salary_deductions' => ['ar' => 'خصومات العمالة', 'en' => 'Salary Deductions'],
            'sidebar.housing.rental_housing.leaves' => ['ar' => 'إجازات العمالة', 'en' => 'Worker Leaves'],
            'sidebar.housing.rental_housing.reports' => ['ar' => 'التقارير', 'en' => 'Reports'],
            'sidebar.housing.rental_housing.drivers' => ['ar' => 'إدارة نقل السائقين', 'en' => 'Drivers Transfer Management'],
            'sidebar.rental_housing.housing_requests' => ['ar' => 'طلبات الإيواء', 'en' => 'Housing Requests'],

            // Dashboard
            'housing.dashboard.heading' => ['ar' => 'لوحة تحكم قسم الإيواء', 'en' => 'Housing Dashboard'],
            'housing.dashboard.title' => ['ar' => 'لوحة تحكم قسم الإيواء', 'en' => 'Housing Dashboard'],
            'housing.dashboard.completed_requests' => ['ar' => 'طلبات مكتملة', 'en' => 'Completed Requests'],
            'housing.dashboard.approved_requests' => ['ar' => 'طلبات موافق عليها', 'en' => 'Approved Requests'],
            'housing.dashboard.pending_requests' => ['ar' => 'طلبات معلقة', 'en' => 'Pending Requests'],
            'housing.dashboard.request_type' => ['ar' => 'نوع الطلب', 'en' => 'Request Type'],
            'housing.dashboard.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'housing.dashboard.from_date' => ['ar' => 'من تاريخ', 'en' => 'From Date'],
            'housing.dashboard.to_date' => ['ar' => 'إلى تاريخ', 'en' => 'To Date'],
            'housing.dashboard.search' => ['ar' => 'بحث', 'en' => 'Search'],
            'housing.dashboard.reset' => ['ar' => 'إعادة تعيين', 'en' => 'Reset'],
            'housing.dashboard.filter_requests' => ['ar' => 'فلترة الطلبات', 'en' => 'Filter Requests'],
            'housing.dashboard.requests_table' => ['ar' => 'جدول الطلبات', 'en' => 'Requests Table'],
            'housing.dashboard.delivery_tasks' => ['ar' => 'مهام التوصيل', 'en' => 'Delivery Tasks'],
            'housing.dashboard.driver_management' => ['ar' => 'إدارة السائقين', 'en' => 'Driver Management'],
            'housing.dashboard.order_reports' => ['ar' => 'تقارير الطلبات', 'en' => 'Order Reports'],
            'housing.dashboard.driver_performance' => ['ar' => 'أداء السائقين', 'en' => 'Driver Performance'],

            // Workers
            'housing.workers.title' => ['ar' => 'العمالة', 'en' => 'Workers'],
            'housing.workers.heading' => ['ar' => 'العمالة', 'en' => 'Workers'],
            'housing.workers.stats.total' => ['ar' => 'إجمالي العمالة', 'en' => 'Total Workers'],
            'housing.workers.stats.stopped' => ['ar' => 'موقوفة', 'en' => 'Stopped'],
            'housing.workers.stats.on_leave' => ['ar' => 'في إجازة', 'en' => 'On Leave'],
            'housing.workers.stats.outside_service' => ['ar' => 'عمالة خارج الخدمة', 'en' => 'Outside Service'],
            'housing.workers.stats.transfer_kafala' => ['ar' => 'نقل الكفالة', 'en' => 'Transfer Kafala'],
            'housing.workers.stats.rented' => ['ar' => 'عمالة مستأجرة', 'en' => 'Rented'],
            'housing.available_workers.title' => ['ar' => 'العمالة المتاحة', 'en' => 'Available Workers'],
            'housing.available_workers.heading' => ['ar' => 'العمالة المتاحة', 'en' => 'Available Workers'],

            // Requests
            'housing.requests.order_no' => ['ar' => 'رقم الطلب', 'en' => 'Order No'],
            'housing.requests.contract_no' => ['ar' => 'رقم العقد', 'en' => 'Contract No'],
            'housing.requests.client' => ['ar' => 'العميل', 'en' => 'Client'],
            'housing.requests.laborer' => ['ar' => 'العامل', 'en' => 'Laborer'],
            'housing.requests.request_type' => ['ar' => 'نوع الطلب', 'en' => 'Request Type'],
            'housing.requests.building' => ['ar' => 'المبنى', 'en' => 'Building'],
            'housing.requests.unit' => ['ar' => 'الوحدة', 'en' => 'Unit'],
            'housing.requests.request_date' => ['ar' => 'تاريخ الطلب', 'en' => 'Request Date'],
            'housing.requests.requested_from' => ['ar' => 'من تاريخ', 'en' => 'From Date'],
            'housing.requests.requested_to' => ['ar' => 'إلى تاريخ', 'en' => 'To Date'],
            'housing.requests.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'housing.requests.notes' => ['ar' => 'ملاحظات', 'en' => 'Notes'],
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

            // Salaries
            'housing.salary_batch.month' => ['ar' => 'الشهر', 'en' => 'Month'],
            'housing.salary_batch.total_salaries' => ['ar' => 'إجمالي الرواتب', 'en' => 'Total Salaries'],
            'housing.salary_batch.total_paid' => ['ar' => 'المدفوع', 'en' => 'Total Paid'],
            'housing.salary_batch.total_pending' => ['ar' => 'المعلق', 'en' => 'Total Pending'],
            'housing.salary_batch.total_deductions' => ['ar' => 'إجمالي الخصومات', 'en' => 'Total Deductions'],
            'housing.salary_batch.workers_count' => ['ar' => 'عدد العمالة', 'en' => 'Workers Count'],

            // Deductions
            'housing.deduction.status.pending' => ['ar' => 'معلق', 'en' => 'Pending'],
            'housing.deduction.status.approved' => ['ar' => 'معتمد', 'en' => 'Approved'],
            'housing.deduction.status.applied' => ['ar' => 'مطبق', 'en' => 'Applied'],
            'forms.housing.deduction.type.fine' => ['ar' => 'غرامة', 'en' => 'Fine'],
            'forms.housing.deduction.type.advance' => ['ar' => 'سلفة', 'en' => 'Advance'],
            'forms.housing.deduction.type.other' => ['ar' => 'أخرى', 'en' => 'Other'],

            // Leaves
            'housing.leave.status.pending' => ['ar' => 'معلق', 'en' => 'Pending'],
            'housing.leave.status.approved' => ['ar' => 'موافق عليه', 'en' => 'Approved'],
            'housing.leave.status.completed' => ['ar' => 'مكتمل', 'en' => 'Completed'],
            'forms.housing.leave.type.annual' => ['ar' => 'سنوية', 'en' => 'Annual'],
            'forms.housing.leave.type.exit_return' => ['ar' => 'خروج وعودة', 'en' => 'Exit and Return'],
            'forms.housing.leave.type.sick' => ['ar' => 'مرضية', 'en' => 'Sick'],
            'forms.housing.leave.type.other' => ['ar' => 'أخرى', 'en' => 'Other'],

            // Reports
            'housing.reports.title' => ['ar' => 'تقارير الإيواء', 'en' => 'Housing Reports'],
            'housing.reports.heading' => ['ar' => 'تقارير الإيواء', 'en' => 'Housing Reports'],
            'housing.reports.filters' => ['ar' => 'فلترة التقرير', 'en' => 'Report Filters'],
            'housing.reports.stats.workers_count' => ['ar' => 'عدد العمال', 'en' => 'Workers Count'],
            'housing.reports.stats.total_contracts' => ['ar' => 'إجمالي العقود', 'en' => 'Total Contracts'],
            'housing.reports.stats.total_amount' => ['ar' => 'إجمالي المبالغ', 'en' => 'Total Amount'],
            'housing.reports.stats.total_work_days' => ['ar' => 'إجمالي أيام العمل', 'en' => 'Total Work Days'],

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
            'actions.search' => ['ar' => 'بحث', 'en' => 'Search'],
            'actions.reset' => ['ar' => 'إعادة تعيين', 'en' => 'Reset'],
            'actions.edit' => ['ar' => 'تعديل', 'en' => 'Edit'],

            // Tables
            'tables.housing.workers.id' => ['ar' => 'رقم', 'en' => 'ID'],
            'tables.housing.workers.image' => ['ar' => 'الصورة', 'en' => 'Image'],
            'tables.housing.workers.name' => ['ar' => 'الاسم', 'en' => 'Name'],
            'tables.housing.workers.nationality' => ['ar' => 'الدولة', 'en' => 'Nationality'],
            'tables.housing.workers.passport' => ['ar' => 'رقم الجواز', 'en' => 'Passport'],
            'tables.housing.workers.profession' => ['ar' => 'المهنة', 'en' => 'Profession'],
            'tables.housing.workers.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'tables.housing.workers.branch' => ['ar' => 'الفرع', 'en' => 'Branch'],
            'tables.housing.workers.rating' => ['ar' => 'التقييم', 'en' => 'Rating'],
            'tables.housing.workers.available' => ['ar' => 'متاح', 'en' => 'Available'],
            'tables.housing.requests.order_no' => ['ar' => 'رقم الطلب', 'en' => 'Order No'],
            'tables.housing.requests.laborer' => ['ar' => 'اسم العامل', 'en' => 'Laborer Name'],
            'tables.housing.requests.request_type' => ['ar' => 'نوع الطلب', 'en' => 'Request Type'],
            'tables.housing.salary_batch.month' => ['ar' => 'الشهر', 'en' => 'Month'],
            'tables.housing.salary_batch.total_salaries' => ['ar' => 'إجمالي الرواتب', 'en' => 'Total Salaries'],
            'tables.housing.salary_batch.total_paid' => ['ar' => 'المدفوع', 'en' => 'Total Paid'],
            'tables.housing.salary_batch.total_pending' => ['ar' => 'المعلق', 'en' => 'Total Pending'],
            'tables.housing.salary_batch.total_deductions' => ['ar' => 'إجمالي الخصومات', 'en' => 'Total Deductions'],
            'tables.housing.salary_batch.workers_count' => ['ar' => 'عدد العمالة', 'en' => 'Workers Count'],
            'tables.housing.deduction.laborer' => ['ar' => 'اسم العامل', 'en' => 'Laborer Name'],
            'tables.housing.deduction.date' => ['ar' => 'تاريخ الخصم', 'en' => 'Deduction Date'],
            'tables.housing.deduction.type' => ['ar' => 'نوع الخصم', 'en' => 'Deduction Type'],
            'tables.housing.deduction.amount' => ['ar' => 'المبلغ', 'en' => 'Amount'],
            'tables.housing.deduction.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'tables.housing.leave.laborer' => ['ar' => 'اسم العامل', 'en' => 'Laborer Name'],
            'tables.housing.leave.type' => ['ar' => 'نوع الإجازة', 'en' => 'Leave Type'],
            'tables.housing.leave.start_date' => ['ar' => 'تاريخ البداية', 'en' => 'Start Date'],
            'tables.housing.leave.end_date' => ['ar' => 'تاريخ النهاية', 'en' => 'End Date'],
            'tables.housing.leave.days' => ['ar' => 'المدة', 'en' => 'Duration'],
            'tables.housing.leave.reason' => ['ar' => 'السبب', 'en' => 'Reason'],
            'tables.housing.leave.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'tables.housing.driver.name' => ['ar' => 'الاسم', 'en' => 'Name'],
            'tables.housing.driver.phone' => ['ar' => 'الجوال', 'en' => 'Phone'],
            'tables.housing.driver.license' => ['ar' => 'رقم الرخصة', 'en' => 'License Number'],
            'tables.housing.driver.license_expiry' => ['ar' => 'انتهاء الرخصة', 'en' => 'License Expiry'],
            'tables.housing.driver.car_type' => ['ar' => 'نوع السيارة', 'en' => 'Car Type'],
            'tables.housing.driver.plate' => ['ar' => 'رقم اللوحة', 'en' => 'Plate Number'],

            // Forms
            'forms.housing.salary_batch.month' => ['ar' => 'الشهر', 'en' => 'Month'],
            'forms.housing.salary_batch.month_helper' => ['ar' => 'مثال: 2026-02', 'en' => 'Example: 2026-02'],
            'forms.housing.deduction.laborer' => ['ar' => 'اسم العامل', 'en' => 'Worker Name'],
            'forms.housing.deduction.date' => ['ar' => 'تاريخ الخصم', 'en' => 'Deduction Date'],
            'forms.housing.deduction.type' => ['ar' => 'نوع الخصم', 'en' => 'Deduction Type'],
            'forms.housing.deduction.amount' => ['ar' => 'المبلغ', 'en' => 'Amount'],
            'forms.housing.deduction.reason' => ['ar' => 'سبب الخصم', 'en' => 'Reason'],
            'forms.housing.deduction.notes' => ['ar' => 'ملاحظات', 'en' => 'Notes'],
            'forms.housing.deduction.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'forms.housing.leave.laborer' => ['ar' => 'اسم العامل', 'en' => 'Worker Name'],
            'forms.housing.leave.type' => ['ar' => 'نوع الإجازة', 'en' => 'Leave Type'],
            'forms.housing.leave.start_date' => ['ar' => 'تاريخ البداية', 'en' => 'Start Date'],
            'forms.housing.leave.days' => ['ar' => 'المدة (أيام)', 'en' => 'Duration (Days)'],
            'forms.housing.leave.end_date' => ['ar' => 'تاريخ النهاية', 'en' => 'End Date'],
            'forms.housing.leave.reason' => ['ar' => 'السبب', 'en' => 'Reason'],
            'forms.housing.leave.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'forms.housing.driver.name' => ['ar' => 'الاسم', 'en' => 'Name'],
            'forms.housing.driver.phone' => ['ar' => 'الجوال', 'en' => 'Phone'],
            'forms.housing.driver.identity' => ['ar' => 'رقم الهوية', 'en' => 'Identity Number'],
            'forms.housing.driver.license' => ['ar' => 'رقم الرخصة', 'en' => 'License Number'],
            'forms.housing.driver.license_expiry' => ['ar' => 'انتهاء الرخصة', 'en' => 'License Expiry'],
            'forms.housing.driver.car_type' => ['ar' => 'نوع السيارة', 'en' => 'Car Type'],
            'forms.housing.driver.car_model' => ['ar' => 'موديل السيارة', 'en' => 'Car Model'],
            'forms.housing.driver.plate' => ['ar' => 'رقم اللوحة', 'en' => 'Plate Number'],

            // Filters
            'filters.housing.building' => ['ar' => 'المبنى', 'en' => 'Building'],
            'filters.housing.status' => ['ar' => 'الحالة', 'en' => 'Status'],
            'filters.housing.available' => ['ar' => 'متاح', 'en' => 'Available'],
            'filters.housing.request_type' => ['ar' => 'نوع الطلب', 'en' => 'Request Type'],
            'filters.housing.driver.license_expiring' => ['ar' => 'رخص منتهية/قريبة الانتهاء', 'en' => 'Expiring/Expired Licenses'],
            'filters.branch' => ['ar' => 'الفرع', 'en' => 'Branch'],

            // Common
            'common.currency' => ['ar' => 'ريال', 'en' => 'SAR'],
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
