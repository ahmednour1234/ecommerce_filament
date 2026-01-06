<?php

namespace Database\Seeders\HR;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class HrAttendanceTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all HR Attendance module translations (Arabic and English).
     */
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping HR Attendance translations.');
            return;
        }

        $this->command->info('Creating HR Attendance module translations...');

        $translations = [
            // Navigation
            'navigation.groups.hr' => ['en' => 'HR', 'ar' => 'الموارد البشرية'],
            'sidebar.hr' => ['en' => 'HR', 'ar' => 'الموارد البشرية'],
            'navigation.groups.hr_attendance' => ['en' => 'HR > Attendance', 'ar' => 'الموارد البشرية > الحضور'],
            'sidebar.hr_attendance' => ['en' => 'Attendance', 'ar' => 'الحضور'],
            'navigation.hr_attendance' => ['en' => 'Attendance', 'ar' => 'الحضور'],
            'navigation.hr_work_places' => ['en' => 'Work Places', 'ar' => 'أماكن العمل'],
            'sidebar.hr_work_places' => ['en' => 'Work Places', 'ar' => 'أماكن العمل'],
            'navigation.hr_employee_groups' => ['en' => 'Employee Groups', 'ar' => 'مجموعات الموظفين'],
            'sidebar.hr_employee_groups' => ['en' => 'Employee Groups', 'ar' => 'مجموعات الموظفين'],
            'navigation.hr_work_schedules' => ['en' => 'Work Schedules', 'ar' => 'مواعيد العمل'],
            'sidebar.hr_work_schedules' => ['en' => 'Work Schedules', 'ar' => 'مواعيد العمل'],
            'navigation.hr_excuse_requests' => ['en' => 'Excuse Requests', 'ar' => 'طلبات الاستئذان'],
            'sidebar.hr_excuse_requests' => ['en' => 'Excuse Requests', 'ar' => 'طلبات الاستئذان'],
            'navigation.hr_devices' => ['en' => 'Fingerprint Devices', 'ar' => 'أجهزة البصمة'],
            'sidebar.hr_devices' => ['en' => 'Fingerprint Devices', 'ar' => 'أجهزة البصمة'],
            'navigation.hr_assign_work_places' => ['en' => 'Assign Work Places', 'ar' => 'تعيين أماكن العمل'],
            'sidebar.hr_assign_work_places' => ['en' => 'Assign Work Places', 'ar' => 'تعيين أماكن العمل'],
            'navigation.hr_copy_schedules' => ['en' => 'Copy Schedules', 'ar' => 'نسخ المواعيد'],
            'sidebar.hr_copy_schedules' => ['en' => 'Copy Schedules', 'ar' => 'نسخ المواعيد'],
            'navigation.hr_daily_attendance' => ['en' => 'Daily Attendance', 'ar' => 'الحضور اليومي'],
            'sidebar.hr_daily_attendance' => ['en' => 'Daily Attendance', 'ar' => 'الحضور اليومي'],
            'navigation.hr_monthly_report' => ['en' => 'Monthly Attendance Report', 'ar' => 'تقرير الحضور الشهري'],
            'sidebar.hr_monthly_report' => ['en' => 'Monthly Attendance Report', 'ar' => 'تقرير الحضور الشهري'],

            // Fields - Work Places
            'fields.latitude' => ['en' => 'Latitude', 'ar' => 'خط العرض'],
            'fields.longitude' => ['en' => 'Longitude', 'ar' => 'خط الطول'],
            'fields.radius_meters' => ['en' => 'Radius (meters)', 'ar' => 'نصف القطر (متر)'],
            'fields.work_place' => ['en' => 'Work Place', 'ar' => 'مكان العمل'],
            'fields.default_schedule' => ['en' => 'Default Schedule', 'ar' => 'الموعد الافتراضي'],

            // Fields - Employee Groups
            'fields.members' => ['en' => 'Members', 'ar' => 'الأعضاء'],
            'fields.members_count' => ['en' => 'Members Count', 'ar' => 'عدد الأعضاء'],

            // Fields - Schedules
            'fields.start_time' => ['en' => 'Start Time', 'ar' => 'وقت البداية'],
            'fields.end_time' => ['en' => 'End Time', 'ar' => 'وقت النهاية'],
            'fields.break_minutes' => ['en' => 'Break Minutes', 'ar' => 'دقائق الاستراحة'],
            'fields.late_grace_minutes' => ['en' => 'Late Grace Minutes', 'ar' => 'دقائق السماح للتأخير'],

            // Fields - Excuse Requests
            'fields.hours' => ['en' => 'Hours', 'ar' => 'ساعات'],
            'fields.reason' => ['en' => 'Reason', 'ar' => 'السبب'],
            'fields.pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'fields.approved' => ['en' => 'Approved', 'ar' => 'موافق عليه'],
            'fields.rejected' => ['en' => 'Rejected', 'ar' => 'مرفوض'],
            'fields.approved_by' => ['en' => 'Approved By', 'ar' => 'تمت الموافقة بواسطة'],
            'fields.approved_at' => ['en' => 'Approved At', 'ar' => 'تمت الموافقة في'],

            // Fields - Attendance
            'fields.first_in' => ['en' => 'First In', 'ar' => 'أول دخول'],
            'fields.last_out' => ['en' => 'Last Out', 'ar' => 'آخر خروج'],
            'fields.worked_minutes' => ['en' => 'Worked Minutes', 'ar' => 'دقائق العمل'],
            'fields.late_minutes' => ['en' => 'Late Minutes', 'ar' => 'دقائق التأخير'],
            'fields.overtime_minutes' => ['en' => 'Overtime Minutes', 'ar' => 'دقائق الإضافي'],
            'fields.present' => ['en' => 'Present', 'ar' => 'حاضر'],
            'fields.absent' => ['en' => 'Absent', 'ar' => 'غائب'],
            'fields.leave' => ['en' => 'Leave', 'ar' => 'إجازة'],
            'fields.holiday' => ['en' => 'Holiday', 'ar' => 'عطلة'],
            'fields.present_days' => ['en' => 'Present Days', 'ar' => 'أيام الحضور'],
            'fields.absent_days' => ['en' => 'Absent Days', 'ar' => 'أيام الغياب'],
            'fields.leave_days' => ['en' => 'Leave Days', 'ar' => 'أيام الإجازة'],
            'fields.holiday_days' => ['en' => 'Holiday Days', 'ar' => 'أيام العطل'],
            'fields.late_days' => ['en' => 'Late Days', 'ar' => 'أيام التأخير'],
            'fields.total_worked_hours' => ['en' => 'Total Worked Hours', 'ar' => 'إجمالي ساعات العمل'],
            'fields.total_overtime_hours' => ['en' => 'Total Overtime Hours', 'ar' => 'إجمالي ساعات الإضافي'],
            'fields.total_late_hours' => ['en' => 'Total Late Hours', 'ar' => 'إجمالي ساعات التأخير'],

            // Fields - Devices
            'fields.type' => ['en' => 'Type', 'ar' => 'النوع'],
            'fields.fingerprint' => ['en' => 'Fingerprint', 'ar' => 'بصمة'],
            'fields.ip_address' => ['en' => 'IP Address', 'ar' => 'عنوان IP'],
            'fields.serial_number' => ['en' => 'Serial Number', 'ar' => 'الرقم التسلسلي'],
            'fields.api_key' => ['en' => 'API Key', 'ar' => 'مفتاح API'],
            'fields.api_key_helper' => ['en' => 'Auto-generated if left empty', 'ar' => 'يتم إنشاؤه تلقائياً إذا تركت فارغاً'],

            // Fields - Schedule Copy
            'fields.copy_from' => ['en' => 'Copy From', 'ar' => 'نسخ من'],
            'fields.source_type' => ['en' => 'Source Type', 'ar' => 'نوع المصدر'],
            'fields.source' => ['en' => 'Source', 'ar' => 'المصدر'],
            'fields.employee_group' => ['en' => 'Employee Group', 'ar' => 'مجموعة الموظفين'],
            'fields.target_employees' => ['en' => 'Target Employees', 'ar' => 'الموظفون المستهدفون'],
            'fields.schedule_details' => ['en' => 'Schedule Details', 'ar' => 'تفاصيل الموعد'],
            'fields.use_source_schedule' => ['en' => 'Use Source Schedule', 'ar' => 'استخدام موعد المصدر'],
            'fields.schedule' => ['en' => 'Schedule', 'ar' => 'الموعد'],
            'fields.date' => ['en' => 'Date', 'ar' => 'التاريخ'],
            'fields.date_from' => ['en' => 'Date From', 'ar' => 'من تاريخ'],
            'fields.date_to' => ['en' => 'Date To', 'ar' => 'إلى تاريخ'],
            'fields.year' => ['en' => 'Year', 'ar' => 'السنة'],
            'fields.month' => ['en' => 'Month', 'ar' => 'الشهر'],
            'fields.none' => ['en' => 'None', 'ar' => 'لا شيء'],

            // Actions
            'actions.show_results' => ['en' => 'Show Employees', 'ar' => 'عرض الموظفين'],
            'actions.copy_schedules' => ['en' => 'Copy Schedules', 'ar' => 'نسخ المواعيد'],
            'actions.apply_holidays' => ['en' => 'Apply Holidays', 'ar' => 'تطبيق العطلات'],

            // Months
            'months.january' => ['en' => 'January', 'ar' => 'يناير'],
            'months.february' => ['en' => 'February', 'ar' => 'فبراير'],
            'months.march' => ['en' => 'March', 'ar' => 'مارس'],
            'months.april' => ['en' => 'April', 'ar' => 'أبريل'],
            'months.may' => ['en' => 'May', 'ar' => 'مايو'],
            'months.june' => ['en' => 'June', 'ar' => 'يونيو'],
            'months.july' => ['en' => 'July', 'ar' => 'يوليو'],
            'months.august' => ['en' => 'August', 'ar' => 'أغسطس'],
            'months.september' => ['en' => 'September', 'ar' => 'سبتمبر'],
            'months.october' => ['en' => 'October', 'ar' => 'أكتوبر'],
            'months.november' => ['en' => 'November', 'ar' => 'نوفمبر'],
            'months.december' => ['en' => 'December', 'ar' => 'ديسمبر'],

            // Messages
            'messages.schedules_copied_successfully' => ['en' => 'Schedules copied successfully', 'ar' => 'تم نسخ المواعيد بنجاح'],
            'messages.holidays_applied' => ['en' => 'Holidays applied', 'ar' => 'تم تطبيق العطلات'],
            'messages.error' => ['en' => 'Error', 'ar' => 'خطأ'],
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

        $this->command->info("✓ HR Attendance translations created: {$created} entries");
    }
}

