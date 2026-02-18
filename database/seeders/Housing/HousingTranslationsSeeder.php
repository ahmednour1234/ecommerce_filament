<?php

namespace Database\Seeders\Housing;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class HousingTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping Housing translations.');
            return;
        }

        $this->command->info('Creating Housing translations...');

        $translations = [
            'sidebar.recruitment_housing' => ['en' => 'Recruitment Housing', 'ar' => 'إيواء الاستقدام'],
            'sidebar.recruitment_housing.dashboard' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
            'sidebar.recruitment_housing.laborers_list' => ['en' => 'Laborers List', 'ar' => 'قائمة العمالة'],
            'sidebar.recruitment_housing.housing_requests' => ['en' => 'Housing Requests', 'ar' => 'طلبات الإيواء'],
            'sidebar.recruitment_housing.laborer_salaries' => ['en' => 'Laborer Salaries', 'ar' => 'رواتب العمالة'],
            'sidebar.recruitment_housing.laborer_leaves' => ['en' => 'Laborer Leaves', 'ar' => 'إجازات العمالة'],
            'sidebar.recruitment_housing.accommodation_entries' => ['en' => 'Accommodation Entries', 'ar' => 'إدخالات الإيواء'],
            'sidebar.recruitment_housing.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],

            'sidebar.rental_housing' => ['en' => 'Rental Housing', 'ar' => 'إيواء التأجير'],
            'sidebar.rental_housing.dashboard' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
            'sidebar.rental_housing.laborers_list' => ['en' => 'Laborers List', 'ar' => 'قائمة العمالة'],
            'sidebar.rental_housing.housing_requests' => ['en' => 'Housing Requests', 'ar' => 'طلبات الإيواء'],
            'sidebar.rental_housing.laborer_salaries' => ['en' => 'Laborer Salaries', 'ar' => 'رواتب العمالة'],
            'sidebar.rental_housing.laborer_leaves' => ['en' => 'Laborer Leaves', 'ar' => 'إجازات العمالة'],
            'sidebar.rental_housing.accommodation_entries' => ['en' => 'Accommodation Entries', 'ar' => 'إدخالات الإيواء'],
            'sidebar.rental_housing.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],

            'sidebar.housing_management' => ['en' => 'Housing Management', 'ar' => 'إدارة الإيواء'],
            'sidebar.housing_management.status_management' => ['en' => 'Status Management', 'ar' => 'إدارة الحالات'],
            'sidebar.housing_management.buildings_management' => ['en' => 'Buildings Management', 'ar' => 'إدارة المباني'],
            'sidebar.housing_management.branches_management' => ['en' => 'Branches Management', 'ar' => 'إدارة الفروع'],

            'sidebar.housing.housing' => ['en' => 'Housing', 'ar' => 'الإيواء'],
            'sidebar.housing.dashboard' => ['en' => 'Dashboard', 'ar' => 'لوحة التحكم'],
            'sidebar.housing.laborers_list' => ['en' => 'Laborers List', 'ar' => 'قائمة العمالة'],
            'sidebar.housing.status_management' => ['en' => 'Status Management', 'ar' => 'إدارة الحالات'],
            'sidebar.housing.branches_management' => ['en' => 'Branches Management', 'ar' => 'إدارة الفروع'],
            'sidebar.housing.reports' => ['en' => 'Reports', 'ar' => 'التقارير'],
            'sidebar.housing.rental_housing' => ['en' => 'Rental Housing', 'ar' => 'إيواء التأجير'],
            'sidebar.housing.available_laborers' => ['en' => 'Available Laborers', 'ar' => 'العمالة المتاحة'],
            'sidebar.housing.housing_requests' => ['en' => 'Housing Requests', 'ar' => 'طلبات الإيواء'],
            'sidebar.housing.laborer_salaries' => ['en' => 'Laborer Salaries', 'ar' => 'رواتب العمالة'],
            'sidebar.housing.laborer_leaves' => ['en' => 'Laborer Leaves', 'ar' => 'إجازات العمالة'],
            'sidebar.housing.worker_evaluations_report' => ['en' => 'Worker Evaluations Report', 'ar' => 'تقرير تقييمات العمالة'],
            'sidebar.housing.worker_contracts_report' => ['en' => 'Worker Contracts Report', 'ar' => 'تقرير عقود العمالة'],
            'sidebar.housing.detailed_contracts_report' => ['en' => 'Detailed Contracts Report', 'ar' => 'تقرير تفصيلي للعقود'],

            'housing.dashboard.heading' => ['en' => 'Housing Dashboard', 'ar' => 'لوحة تحكم الإيواء'],
            'housing.dashboard.completed_requests' => ['en' => 'Completed Requests', 'ar' => 'طلبات مكتملة'],
            'housing.dashboard.approved_requests' => ['en' => 'Approved Requests', 'ar' => 'طلبات موافق عليها'],
            'housing.dashboard.pending_requests' => ['en' => 'Pending Requests', 'ar' => 'طلبات معلقة'],
            'housing.dashboard.delivery_tasks' => ['en' => 'Delivery Tasks', 'ar' => 'مهام التوصيل'],
            'housing.dashboard.driver_management' => ['en' => 'Driver Management', 'ar' => 'إدارة السائقين'],
            'housing.dashboard.order_reports' => ['en' => 'Order Reports', 'ar' => 'تقارير الطلبات'],
            'housing.dashboard.driver_performance' => ['en' => 'Driver Performance', 'ar' => 'أداء السائقين'],
            'housing.dashboard.filter_requests' => ['en' => 'Filter Requests', 'ar' => 'فلترة الطلبات'],
            'housing.dashboard.request_type' => ['en' => 'Request Type', 'ar' => 'نوع الطلب'],
            'housing.dashboard.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'housing.dashboard.from_date' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'housing.dashboard.to_date' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'housing.dashboard.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'housing.dashboard.reset' => ['en' => 'Reset', 'ar' => 'إعادة تعيين'],

            'housing.requests.pending' => ['en' => 'Pending Requests', 'ar' => 'طلبات معلّقة'],
            'housing.requests.order_no' => ['en' => 'Order No', 'ar' => 'رقم الطلب'],
            'housing.requests.contract_no' => ['en' => 'Contract No', 'ar' => 'رقم العقد'],
            'housing.requests.client' => ['en' => 'Client', 'ar' => 'العميل'],
            'housing.requests.laborer' => ['en' => 'Laborer', 'ar' => 'العمالة'],
            'housing.requests.type' => ['en' => 'Type', 'ar' => 'نوع الطلب'],
            'housing.requests.request_date' => ['en' => 'Request Date', 'ar' => 'تاريخ الطلب'],
            'housing.requests.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'housing.requests.actions' => ['en' => 'Actions', 'ar' => 'الإجراءات'],
            'housing.requests.type.delivery' => ['en' => 'Delivery', 'ar' => 'تسليم'],
            'housing.requests.type.return' => ['en' => 'Return', 'ar' => 'استرجاع'],
            'housing.requests.type.new_arrival' => ['en' => 'New Arrival', 'ar' => 'وافد جديد'],
            'housing.requests.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'housing.requests.select_status' => ['en' => 'Select option / اختر', 'ar' => 'Select option / اختر'],

            'housing.salary.create' => ['en' => 'Add Salary for Worker', 'ar' => 'إضافة راتب للعامل'],
            'housing.salary.employee' => ['en' => 'Employee', 'ar' => 'اسم العامل'],
            'housing.salary.month' => ['en' => 'Month', 'ar' => 'الشهر'],
            'housing.salary.basic_salary' => ['en' => 'Basic Salary', 'ar' => 'الراتب الأساسي'],
            'housing.salary.overtime_hours' => ['en' => 'Overtime Hours', 'ar' => 'ساعات العمل الإضافي'],
            'housing.salary.overtime_amount' => ['en' => 'Overtime Amount', 'ar' => 'مبلغ العمل الإضافي'],
            'housing.salary.bonuses' => ['en' => 'Bonuses', 'ar' => 'المكافآت'],
            'housing.salary.deductions' => ['en' => 'Total Deductions', 'ar' => 'إجمالي الخصومات'],
            'housing.salary.net_salary' => ['en' => 'Net Salary', 'ar' => 'الراتب الصافي'],
            'housing.salary.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],

            'housing.leave.create' => ['en' => 'Add New Leave', 'ar' => 'إضافة إجازة جديدة'],
            'housing.leave.employee' => ['en' => 'Employee', 'ar' => 'اسم العامل'],
            'housing.leave.leave_type' => ['en' => 'Leave Type', 'ar' => 'نوع الإجازة'],
            'housing.leave.start_date' => ['en' => 'Leave Start Date', 'ar' => 'تاريخ بداية الإجازة'],
            'housing.leave.days' => ['en' => 'Number of Days', 'ar' => 'عدد الأيام'],
            'housing.leave.end_date' => ['en' => 'Leave End Date', 'ar' => 'تاريخ نهاية الإجازة'],
            'housing.leave.status' => ['en' => 'Leave Status', 'ar' => 'حالة الإجازة'],
            'housing.leave.reason' => ['en' => 'Leave Reason', 'ar' => 'سبب الإجازة'],
            'housing.leave.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'housing.leave.status.pending' => ['en' => 'Pending', 'ar' => 'معلقة'],

            'housing.accommodation.create' => ['en' => 'Add New Accommodation Entry', 'ar' => 'إضافة إدخال إيواء جديد'],
            'housing.accommodation.laborer' => ['en' => 'Laborer', 'ar' => 'العامل'],
            'housing.accommodation.contract_no' => ['en' => 'Contract No', 'ar' => 'رقم العقد'],
            'housing.accommodation.entry_type' => ['en' => 'Entry Type', 'ar' => 'نوع الدخول'],
            'housing.accommodation.entry_date' => ['en' => 'Entry Date', 'ar' => 'تاريخ الدخول'],
            'housing.accommodation.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'housing.accommodation.building' => ['en' => 'Building', 'ar' => 'المبنى'],
            'housing.accommodation.entry_type.new_arrival' => ['en' => 'New Arrival', 'ar' => 'وافد جديد'],
            'housing.accommodation.entry_type.return' => ['en' => 'Return', 'ar' => 'استرجاع'],
            'housing.accommodation.entry_type.transfer' => ['en' => 'Transfer', 'ar' => 'نقل'],
            'housing.accommodation.available_buildings_note' => ['en' => 'Only buildings with available capacity > 0 are shown', 'ar' => 'يتم عرض المباني المتاحة فقط (السعة المتاحة > 0)'],

            'housing.status.management' => ['en' => 'Status Management', 'ar' => 'إدارة الحالات'],
            'housing.status.key' => ['en' => 'Status Key', 'ar' => 'مفتاح الحالة'],
            'housing.status.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم بالعربية'],
            'housing.status.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم بالإنجليزية'],
            'housing.status.color' => ['en' => 'Color', 'ar' => 'اللون'],
            'housing.status.icon' => ['en' => 'Icon', 'ar' => 'الأيقونة'],
            'housing.status.order' => ['en' => 'Order', 'ar' => 'الترتيب'],
            'housing.status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'housing.status.unpaid_salary' => ['en' => 'Unpaid Salary', 'ar' => 'عدم دفع الراتب'],
            'housing.status.issue' => ['en' => 'Issue', 'ar' => 'مشكلة'],
            'housing.status.transfer_sponsorship' => ['en' => 'Sponsorship Transfer', 'ar' => 'نقل كفاله'],
            'housing.status.work_refused' => ['en' => 'Work Refused', 'ar' => 'رفض العمل'],
            'housing.status.runaway' => ['en' => 'Runaway', 'ar' => 'هروب'],
            'housing.status.dispute' => ['en' => 'Dispute', 'ar' => 'نزاع'],
            'housing.status.ready_for_delivery' => ['en' => 'Ready for Delivery', 'ar' => 'جاهز للتسليم'],
            'housing.status.with_client' => ['en' => 'With Client', 'ar' => 'مع العميل'],
            'housing.status.in_completion' => ['en' => 'In Completion', 'ar' => 'في الإيماء'],
            'housing.status.completed' => ['en' => 'Completed', 'ar' => 'مكتمل'],

            'housing.reports.heading' => ['en' => 'Housing Reports', 'ar' => 'تقارير الإيواء'],
            'housing.reports.returns_this_month' => ['en' => 'Returns This Month', 'ar' => 'استرجاع هذا الشهر'],
            'housing.reports.exits_this_month' => ['en' => 'Exits This Month', 'ar' => 'خروج هذا الشهر'],
            'housing.reports.entries_this_month' => ['en' => 'Entries This Month', 'ar' => 'دخول هذا الشهر'],
            'housing.reports.current_residents' => ['en' => 'Current Residents', 'ar' => 'المقيمين الحاليين'],
            'housing.reports.return_report' => ['en' => 'Return Report', 'ar' => 'تقرير الاسترجاع'],
            'housing.reports.status_report' => ['en' => 'Status Report', 'ar' => 'تقرير الحالات'],
            'housing.reports.movements_report' => ['en' => 'Movements Report', 'ar' => 'تقرير الحركات'],
            'housing.reports.occupancy_report' => ['en' => 'Occupancy Report', 'ar' => 'تقرير الإشغال'],
            'housing.reports.events_report' => ['en' => 'Events Report', 'ar' => 'تقرير الأحداث'],
            'housing.reports.branches_report' => ['en' => 'Branches Report', 'ar' => 'تقرير الفروع'],
            'housing.reports.accommodation_duration_report' => ['en' => 'Accommodation Duration Report', 'ar' => 'تقرير مدة الإيواء'],
            'housing.reports.return_frequency_report' => ['en' => 'Return Frequency Report', 'ar' => 'تقرير تكرار الاسترجاع'],
            'housing.reports.view_report' => ['en' => 'View Report', 'ar' => 'عرض التقرير'],

            'housing.actions.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'housing.actions.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'housing.actions.edit' => ['en' => 'Edit', 'ar' => 'تعديل'],
            'housing.actions.delete' => ['en' => 'Delete', 'ar' => 'حذف'],
            'housing.actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'housing.actions.excel' => ['en' => 'Excel', 'ar' => 'إكسل'],
            'housing.actions.copy' => ['en' => 'Copy', 'ar' => 'نسخ'],

            'housing.buildings.capacity' => ['en' => 'Capacity', 'ar' => 'السعة'],
            'housing.buildings.available_capacity' => ['en' => 'Available Capacity', 'ar' => 'السعة المتاحة'],
            'housing.buildings.maintenance' => ['en' => 'Maintenance', 'ar' => 'صيانة'],
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

        $this->command->info("✓ Housing translations created: {$created}, updated: {$updated}");
    }
}
