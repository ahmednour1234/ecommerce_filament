<?php

namespace Database\Seeders\HR;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class HrNotificationsTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping HR Notifications translations.');
            return;
        }

        $this->command->info('Creating HR Notifications translations...');

        $translations = [
            // Sidebar
            'sidebar.hr.notifications' => ['en' => 'Notifications', 'ar' => 'التنبيهات'],

            // Types
            'types.leave_request' => ['en' => 'Leave Request', 'ar' => 'طلب إجازة'],
            'types.loan' => ['en' => 'Loan', 'ar' => 'سلفة'],
            'types.excuse_request' => ['en' => 'Excuse Request', 'ar' => 'طلب استئذان'],
            'types.deduction' => ['en' => 'Deduction', 'ar' => 'خصم'],
            'types.attendance_entry' => ['en' => 'Attendance Entry', 'ar' => 'دخول حضور'],
            'types.payroll' => ['en' => 'Payroll', 'ar' => 'كشف مرتبات'],

            // Status
            'status.unread' => ['en' => 'Unread', 'ar' => 'غير مقروء'],
            'status.read' => ['en' => 'Read', 'ar' => 'مقروء'],
            'status.action_taken' => ['en' => 'Action Taken', 'ar' => 'تم اتخاذ إجراء'],

            // Fields
            'fields.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'fields.message' => ['en' => 'Message', 'ar' => 'الرسالة'],
            'fields.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'fields.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'fields.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],

            // Tables
            'tables.hr_notifications.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'tables.hr_notifications.message' => ['en' => 'Message', 'ar' => 'الرسالة'],
            'tables.hr_notifications.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.hr_notifications.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'tables.hr_notifications.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.hr_notifications.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.hr_notifications.created_at' => ['en' => 'Created At', 'ar' => 'تاريخ الإنشاء'],
            'tables.hr_notifications.filters.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'tables.hr_notifications.filters.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.hr_notifications.filters.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.hr_notifications.group_by_type' => ['en' => 'Group by Type', 'ar' => 'تجميع حسب النوع'],
            'tables.hr_notifications.group_by_status' => ['en' => 'Group by Status', 'ar' => 'تجميع حسب الحالة'],

            // Actions
            'actions.mark_as_read' => ['en' => 'Mark as Read', 'ar' => 'تحديد كمقروء'],
            'actions.view_related' => ['en' => 'View Related', 'ar' => 'عرض المرتبط'],

            // Tabs
            'tabs.all' => ['en' => 'All', 'ar' => 'الكل'],
            'tabs.unread' => ['en' => 'Unread', 'ar' => 'غير مقروء'],
            'tabs.leave_requests' => ['en' => 'Leave Requests', 'ar' => 'طلبات الإجازات'],
            'tabs.loans' => ['en' => 'Loans', 'ar' => 'السلف'],
            'tabs.excuse_requests' => ['en' => 'Excuse Requests', 'ar' => 'طلبات الاستئذان'],
            'tabs.deductions' => ['en' => 'Deductions', 'ar' => 'الخصومات'],
            'tabs.attendance_entries' => ['en' => 'Attendance Entries', 'ar' => 'دخول الحضور'],
            'tabs.payroll' => ['en' => 'Payroll', 'ar' => 'كشوف المرتبات'],
        ];

        foreach ($translations as $key => $values) {
            // English translation
            Translation::updateOrCreate(
                [
                    'key' => $key,
                    'language_id' => $english->id,
                ],
                [
                    'value' => $values['en'],
                ]
            );

            // Arabic translation
            Translation::updateOrCreate(
                [
                    'key' => $key,
                    'language_id' => $arabic->id,
                ],
                [
                    'value' => $values['ar'],
                ]
            );
        }

        $this->command->info('✓ HR Notifications translations created: ' . count($translations) * 2);
    }
}
