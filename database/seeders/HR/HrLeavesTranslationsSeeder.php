<?php

namespace Database\Seeders\HR;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class HrLeavesTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all HR Leaves module translations (Arabic and English).
     */
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping HR Leaves translations.');
            return;
        }

        $this->command->info('Creating HR Leaves module translations...');

        $translations = [
            // Navigation
            'navigation.hr_leaves' => ['en' => 'Leaves', 'ar' => 'الإجازات'],
            'navigation.hr_leave_types' => ['en' => 'Leave Types', 'ar' => 'أنواع الإجازات'],
            'navigation.hr_leave_requests' => ['en' => 'Leave Requests', 'ar' => 'طلبات الإجازات'],
            'navigation.hr_leave_balance' => ['en' => 'Leave Balance', 'ar' => 'رصيد الإجازات'],
            'navigation.hr_leave_reports' => ['en' => 'Leave Reports', 'ar' => 'تقرير الإجازات'],

            // Pages
            'pages.hr_leave_types.create' => ['en' => 'Create Leave Type', 'ar' => 'إنشاء نوع إجازة'],
            'pages.hr_leave_requests.title' => ['en' => 'Leave Requests', 'ar' => 'طلبات الإجازات'],
            'pages.hr_leave_requests.create' => ['en' => 'Create Leave Request', 'ar' => 'إنشاء طلب إجازة'],
            'pages.hr_leave_requests.my_requests' => ['en' => 'My Leave Requests', 'ar' => 'طلبات الإجازات الخاصة بي'],
            'pages.hr_leave_balance.title' => ['en' => 'Leave Balance', 'ar' => 'رصيد الإجازات'],
            'pages.hr_leave_balance.heading' => ['en' => 'Leave Balance', 'ar' => 'رصيد الإجازات'],
            'pages.hr_leave_reports.title' => ['en' => 'Leave Reports', 'ar' => 'تقرير الإجازات'],
            'pages.hr_leave_reports.heading' => ['en' => 'Leave Reports', 'ar' => 'تقرير الإجازات'],
            'pages.hr_leave_reports.summary' => ['en' => 'Report Summary', 'ar' => 'ملخص التقرير'],

            // Fields
            'fields.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'fields.leave_type' => ['en' => 'Leave Type', 'ar' => 'نوع الإجازة'],
            'fields.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البداية'],
            'fields.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ النهاية'],
            'fields.total_days' => ['en' => 'Total Days', 'ar' => 'إجمالي الأيام'],
            'fields.reason' => ['en' => 'Reason', 'ar' => 'السبب'],
            'fields.attachment' => ['en' => 'Attachment', 'ar' => 'مرفق'],
            'fields.manager_note' => ['en' => 'Manager Note', 'ar' => 'ملاحظة المدير'],
            'fields.allowed_days_per_year' => ['en' => 'Allowed Days Per Year', 'ar' => 'الأيام المسموحة سنوياً'],
            'fields.year' => ['en' => 'Year', 'ar' => 'السنة'],
            'fields.quota' => ['en' => 'Yearly Quota', 'ar' => 'الحصة السنوية'],
            'fields.used' => ['en' => 'Used', 'ar' => 'المستخدم'],
            'fields.remaining' => ['en' => 'Remaining', 'ar' => 'المتبقي'],
            'fields.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'fields.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'fields.select_option' => ['en' => 'Select an option', 'ar' => 'اختر خياراً'],
            'fields.no_file_chosen' => ['en' => 'No file chosen', 'ar' => 'لم يتم اختيار ملف'],

            // Status
            'status.pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'status.approved' => ['en' => 'Approved', 'ar' => 'موافق عليه'],
            'status.rejected' => ['en' => 'Rejected', 'ar' => 'مرفوض'],
            'status.cancelled' => ['en' => 'Cancelled', 'ar' => 'ملغي'],
            'status.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'status.inactive' => ['en' => 'Inactive', 'ar' => 'غير نشط'],

            // Actions
            'actions.create' => ['en' => 'Create', 'ar' => 'إنشاء'],
            'actions.create_another' => ['en' => 'Create & create another', 'ar' => 'إنشاء وإنشاء آخر'],
            'actions.list' => ['en' => 'List', 'ar' => 'قائمة'],
            'actions.add' => ['en' => 'Add', 'ar' => 'إضافة'],
            'actions.approve' => ['en' => 'Approve', 'ar' => 'موافقة'],
            'actions.reject' => ['en' => 'Reject', 'ar' => 'رفض'],
            'actions.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'actions.recalculate_balances' => ['en' => 'Recalculate Balances', 'ar' => 'إعادة حساب الأرصدة'],
            'actions.show_summary' => ['en' => 'Show Summary', 'ar' => 'عرض الملخص'],
            'actions.export_excel' => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'actions.export_pdf' => ['en' => 'Export to PDF', 'ar' => 'تصدير إلى PDF'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'actions.close' => ['en' => 'Close', 'ar' => 'إغلاق'],
            'actions.remove_item' => ['en' => 'Remove item', 'ar' => 'إزالة عنصر'],
            'actions.remove_filter' => ['en' => 'Remove filter', 'ar' => 'إزالة الفلتر'],
            'actions.toggle_columns' => ['en' => 'Toggle columns', 'ar' => 'تبديل الأعمدة'],

            // Forms - Leave Types
            'forms.hr_leave_types.name_ar' => ['en' => 'Leave Type Name (AR)', 'ar' => 'اسم نوع الإجازة (عربي)'],
            'forms.hr_leave_types.name_en' => ['en' => 'Leave Type Name (EN)', 'ar' => 'اسم نوع الإجازة (إنجليزي)'],
            'forms.hr_leave_types.allowed_days_per_year' => ['en' => 'Allowed Days Per Year', 'ar' => 'عدد الأيام السنوية'],
            'forms.hr_leave_types.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'forms.hr_leave_types.description_ar' => ['en' => 'Description (AR)', 'ar' => 'الوصف (عربي)'],
            'forms.hr_leave_types.description_en' => ['en' => 'Description (EN)', 'ar' => 'الوصف (إنجليزي)'],

            // Tables - Leave Types
            'tables.hr_leave_types.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_leave_types.allowed_days' => ['en' => 'Allowed Days', 'ar' => 'الأيام المسموحة'],
            'tables.hr_leave_types.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.hr_leave_types.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_leave_types.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_leave_types.filters.status' => ['en' => 'Status', 'ar' => 'الحالة'],

            // Tables - Leave Requests
            'tables.hr_leave_requests.employee_number' => ['en' => 'Employee Number', 'ar' => 'رقم الموظف'],
            'tables.hr_leave_requests.employee_name' => ['en' => 'Employee Name', 'ar' => 'اسم الموظف'],
            'tables.hr_leave_requests.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'tables.hr_leave_requests.leave_type' => ['en' => 'Leave Type', 'ar' => 'نوع الإجازة'],
            'tables.hr_leave_requests.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البداية'],
            'tables.hr_leave_requests.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ النهاية'],
            'tables.hr_leave_requests.total_days' => ['en' => 'Total Days', 'ar' => 'إجمالي الأيام'],
            'tables.hr_leave_requests.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.hr_leave_requests.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_leave_requests.filters.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.hr_leave_requests.filters.leave_type' => ['en' => 'Leave Type', 'ar' => 'نوع الإجازة'],

            // Tables - Leave Balance
            'tables.hr_leave_balance.employee_number' => ['en' => 'Employee Number', 'ar' => 'رقم الموظف'],
            'tables.hr_leave_balance.employee_name' => ['en' => 'Employee Name', 'ar' => 'اسم الموظف'],
            'tables.hr_leave_balance.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'tables.hr_leave_balance.leave_type' => ['en' => 'Leave Type', 'ar' => 'نوع الإجازة'],
            'tables.hr_leave_balance.year' => ['en' => 'Year', 'ar' => 'السنة'],
            'tables.hr_leave_balance.quota' => ['en' => 'Yearly Quota', 'ar' => 'الحصة السنوية'],
            'tables.hr_leave_balance.used' => ['en' => 'Used', 'ar' => 'المستخدم'],
            'tables.hr_leave_balance.remaining' => ['en' => 'Remaining', 'ar' => 'المتبقي'],
            'tables.hr_leave_balance.filters.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'tables.hr_leave_balance.filters.year' => ['en' => 'Year', 'ar' => 'السنة'],

            // Tables - Leave Reports
            'tables.hr_leave_reports.employee_number' => ['en' => 'Employee Number', 'ar' => 'رقم الموظف'],
            'tables.hr_leave_reports.employee_name' => ['en' => 'Employee Name', 'ar' => 'اسم الموظف'],
            'tables.hr_leave_reports.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'tables.hr_leave_reports.leave_type' => ['en' => 'Leave Type', 'ar' => 'نوع الإجازة'],
            'tables.hr_leave_reports.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البداية'],
            'tables.hr_leave_reports.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ النهاية'],
            'tables.hr_leave_reports.total_days' => ['en' => 'Total Days', 'ar' => 'إجمالي الأيام'],
            'tables.hr_leave_reports.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.hr_leave_reports.filters.year' => ['en' => 'Year', 'ar' => 'السنة'],
            'tables.hr_leave_reports.filters.month' => ['en' => 'Month', 'ar' => 'الشهر'],
            'tables.hr_leave_reports.filters.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'tables.hr_leave_reports.filters.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'tables.hr_leave_reports.filters.leave_type' => ['en' => 'Leave Type', 'ar' => 'نوع الإجازة'],
            'tables.hr_leave_reports.filters.status' => ['en' => 'Status', 'ar' => 'الحالة'],

            // Stats
            'stats.total_requests' => ['en' => 'Total Requests', 'ar' => 'إجمالي الطلبات'],
            'stats.pending_requests' => ['en' => 'Pending Requests', 'ar' => 'قيد الانتظار'],
            'stats.approved_requests' => ['en' => 'Approved Requests', 'ar' => 'تمت الموافقة'],
            'stats.rejected_requests' => ['en' => 'Rejected Requests', 'ar' => 'مرفوضة'],

            // UI Labels
            'ui.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'ui.filter' => ['en' => 'Filter', 'ar' => 'فلتر'],
            'ui.active_filters' => ['en' => 'Active filters', 'ar' => 'الفلاتر النشطة'],
            'ui.no_leave_requests' => ['en' => 'No leave requests', 'ar' => 'لا توجد طلبات إجازة'],
            'ui.no_leave_balances' => ['en' => 'No leave balances', 'ar' => 'لا توجد أرصدة إجازة'],

            // Messages
            'messages.recalculate_balances_confirmation' => ['en' => 'This will recalculate all leave balances for the selected year. Continue?', 'ar' => 'سيتم إعادة حساب جميع أرصدة الإجازات للسنة المحددة. متابعة؟'],
            'messages.balances_recalculated' => ['en' => 'Balances recalculated successfully.', 'ar' => 'تم إعادة حساب الأرصدة بنجاح.'],
        ];

        $created = 0;
        foreach ($translations as $key => $values) {
            // English translation
            if (isset($values['en'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $english->id,
                    ],
                    ['value' => $values['en']]
                );
                $created++;
            }

            // Arabic translation
            if (isset($values['ar'])) {
                Translation::updateOrCreate(
                    [
                        'key' => $key,
                        'group' => 'dashboard',
                        'language_id' => $arabic->id,
                    ],
                    ['value' => $values['ar']]
                );
                $created++;
            }
        }

        $this->command->info("✓ HR Leaves translations created: {$created} entries");
    }
}

