<?php

namespace Database\Seeders\HR;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class HrTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all HR module translations (Arabic and English).
     */
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping HR translations.');
            return;
        }

        $this->command->info('Creating HR module translations...');

        $translations = [
            // Navigation Group
            'navigation.groups.hr' => ['en' => 'HR', 'ar' => 'الموارد البشرية'],
            'sidebar.hr' => ['en' => 'HR', 'ar' => 'الموارد البشرية'],

            // Navigation Items
            'navigation.hr_departments' => ['en' => 'Departments', 'ar' => 'الإدارات'],
            'navigation.hr_positions' => ['en' => 'Positions', 'ar' => 'المسميات الوظيفية'],
            'navigation.hr_blood_types' => ['en' => 'Blood Types', 'ar' => 'فصائل الدم'],
            'navigation.hr_identity_types' => ['en' => 'Identity Types', 'ar' => 'نوع الهوية'],
            'navigation.hr_banks' => ['en' => 'Banks', 'ar' => 'البنوك'],
            'navigation.hr_employees' => ['en' => 'Employees', 'ar' => 'الموظفين'],
            'navigation.hr_holidays' => ['en' => 'Official Holidays', 'ar' => 'العطلات الرسمية'],
            'navigation.hr_holidays_calendar' => ['en' => 'Holidays Calendar', 'ar' => 'تقويم العطلات'],

            // Common Fields
            'fields.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'fields.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'fields.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'fields.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'fields.employee_number' => ['en' => 'Employee Number', 'ar' => 'رقم الموظف'],
            'fields.first_name' => ['en' => 'First Name', 'ar' => 'الاسم الأول'],
            'fields.last_name' => ['en' => 'Last Name', 'ar' => 'اسم العائلة'],
            'fields.email' => ['en' => 'Email', 'ar' => 'البريد الإلكتروني'],
            'fields.phone' => ['en' => 'Phone', 'ar' => 'الهاتف'],
            'fields.gender' => ['en' => 'Gender', 'ar' => 'الجنس'],
            'fields.birth_date' => ['en' => 'Birth Date', 'ar' => 'تاريخ الميلاد'],
            'fields.fingerprint_device_id' => ['en' => 'Fingerprint Device ID', 'ar' => 'رقم الموظف بجهاز البصمة'],
            'fields.profile_image' => ['en' => 'Profile Image', 'ar' => 'صورة الملف الشخصي'],
            'fields.hire_date' => ['en' => 'Hire Date', 'ar' => 'تاريخ التوظيف'],
            'fields.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'fields.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'fields.position' => ['en' => 'Position', 'ar' => 'المنصب'],
            'fields.location' => ['en' => 'Location', 'ar' => 'الموقع'],
            'fields.basic_salary' => ['en' => 'Basic Salary', 'ar' => 'الراتب الأساسي'],
            'fields.cv_file' => ['en' => 'CV File', 'ar' => 'السيرة الذاتية'],
            'fields.cv_file.helper' => ['en' => 'Upload CV/Resume file', 'ar' => 'رفع ملف السيرة الذاتية'],
            'fields.address' => ['en' => 'Address', 'ar' => 'العنوان'],
            'fields.city' => ['en' => 'City', 'ar' => 'المدينة'],
            'fields.country' => ['en' => 'Country', 'ar' => 'الدولة'],
            'fields.identity_type' => ['en' => 'Identity Type', 'ar' => 'نوع الهوية'],
            'fields.identity_number' => ['en' => 'Identity Number', 'ar' => 'رقم الهوية'],
            'fields.identity_expiry_date' => ['en' => 'Identity Expiry Date', 'ar' => 'تاريخ انتهاء الهوية'],
            'fields.blood_type' => ['en' => 'Blood Type', 'ar' => 'فصيلة الدم'],
            'fields.emergency_contact_name' => ['en' => 'Emergency Contact Name', 'ar' => 'اسم جهة الاتصال للطوارئ'],
            'fields.emergency_contact_phone' => ['en' => 'Emergency Contact Phone', 'ar' => 'هاتف جهة الاتصال للطوارئ'],
            'fields.bank' => ['en' => 'Bank', 'ar' => 'البنك'],
            'fields.bank_name_text' => ['en' => 'Bank Name (if not in list)', 'ar' => 'اسم البنك (إذا لم يكن في القائمة)'],
            'fields.bank_account_number' => ['en' => 'Bank Account Number', 'ar' => 'رقم الحساب'],
            'fields.iban' => ['en' => 'IBAN', 'ar' => 'رقم الايبان'],
            'fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'fields.status_active' => ['en' => 'Active', 'ar' => 'نشط'],
            'fields.status_inactive' => ['en' => 'Inactive', 'ar' => 'غير نشط'],
            'fields.holiday_name' => ['en' => 'Holiday Name', 'ar' => 'اسم العطلة'],
            'fields.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ بداية العطلة'],
            'fields.days_count' => ['en' => 'Days Count', 'ar' => 'عدد الأيام'],
            'fields.days_count.helper' => ['en' => 'Number of days for this holiday', 'ar' => 'عدد الأيام (>=1)'],
            'fields.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ نهاية العطلة'],

            // Actions
            'actions.add' => ['en' => 'Add', 'ar' => 'إضافة'],
            'actions.save' => ['en' => 'Save', 'ar' => 'حفظ'],
            'actions.cancel' => ['en' => 'Cancel', 'ar' => 'إلغاء'],
            'actions.export_excel' => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'actions.export_pdf' => ['en' => 'Export to PDF', 'ar' => 'تصدير إلى PDF'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'actions.add_new_holiday' => ['en' => 'Add New Holiday', 'ar' => 'إضافة عطلة جديدة'],
            'actions.show_calendar' => ['en' => 'Show Calendar', 'ar' => 'عرض التقويم'],
            'actions.holiday_list' => ['en' => 'Holiday List', 'ar' => 'قائمة العطلات'],

            // Messages
            'messages.saved_successfully' => ['en' => 'Saved successfully', 'ar' => 'تم الحفظ بنجاح'],
            'messages.employee_created' => ['en' => 'Employee created successfully', 'ar' => 'تم إنشاء الموظف بنجاح'],
            'messages.employee_updated' => ['en' => 'Employee updated successfully', 'ar' => 'تم تحديث الموظف بنجاح'],
            'messages.no_holidays' => ['en' => 'No holidays found. Please add holidays to view them on the calendar.', 'ar' => 'لا توجد عطلات. يرجى إضافة العطلات لعرضها في التقويم.'],
            'messages.no_holidays.info' => ['en' => 'Click "Add New Holiday" button to create your first holiday.', 'ar' => 'انقر على زر "إضافة عطلة جديدة" لإنشاء أول عطلة.'],

            // Tabs
            'tabs.basic_info' => ['en' => 'Basic Info', 'ar' => 'المعلومات الأساسية'],
            'tabs.job_info' => ['en' => 'Job Info', 'ar' => 'معلومات الوظيفة'],
            'tabs.personal_info' => ['en' => 'Personal Info', 'ar' => 'المعلومات الشخصية'],
            'tabs.bank_info' => ['en' => 'Bank Info', 'ar' => 'معلومات البنك'],

            // Gender
            'gender.male' => ['en' => 'Male', 'ar' => 'ذكر'],
            'gender.female' => ['en' => 'Female', 'ar' => 'أنثى'],

            // Departments Forms
            'forms.hr_departments.name.label' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.hr_departments.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Departments Tables
            'tables.hr_departments.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_departments.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_departments.positions_count' => ['en' => 'Positions', 'ar' => 'الوظائف'],
            'tables.hr_departments.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_departments.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_departments.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Positions Forms
            'forms.hr_positions.title.label' => ['en' => 'Title', 'ar' => 'العنوان'],
            'forms.hr_positions.department_id.label' => ['en' => 'Department', 'ar' => 'الإدارة'],
            'forms.hr_positions.description.label' => ['en' => 'Description', 'ar' => 'الوصف'],
            'forms.hr_positions.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Positions Tables
            'tables.hr_positions.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'tables.hr_positions.department' => ['en' => 'Department', 'ar' => 'الإدارة'],
            'tables.hr_positions.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'tables.hr_positions.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_positions.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_positions.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_positions.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_positions.filters.department' => ['en' => 'Department', 'ar' => 'الإدارة'],

            // Blood Types Forms
            'forms.hr_blood_types.name.label' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.hr_blood_types.code.label' => ['en' => 'Code', 'ar' => 'الرمز'],
            'forms.hr_blood_types.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Blood Types Tables
            'tables.hr_blood_types.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_blood_types.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.hr_blood_types.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_blood_types.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_blood_types.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_blood_types.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Identity Types Forms
            'forms.hr_identity_types.name.label' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.hr_identity_types.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Identity Types Tables
            'tables.hr_identity_types.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_identity_types.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_identity_types.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_identity_types.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_identity_types.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Banks Forms
            'forms.hr_banks.name.label' => ['en' => 'Name', 'ar' => 'الاسم'],
            'forms.hr_banks.iban_prefix.label' => ['en' => 'IBAN Prefix', 'ar' => 'بادئة IBAN'],
            'forms.hr_banks.iban_prefix.helper' => ['en' => 'Optional IBAN prefix code', 'ar' => 'رمز البادئة IBAN (اختياري)'],
            'forms.hr_banks.active.label' => ['en' => 'Active', 'ar' => 'نشط'],

            // Banks Tables
            'tables.hr_banks.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_banks.iban_prefix' => ['en' => 'IBAN Prefix', 'ar' => 'بادئة IBAN'],
            'tables.hr_banks.active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_banks.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_banks.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_banks.filters.active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Employees Tables
            'tables.hr_employees.employee_number' => ['en' => 'Employee Number', 'ar' => 'رقم الموظف'],
            'tables.hr_employees.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.hr_employees.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.hr_employees.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'tables.hr_employees.position' => ['en' => 'Position', 'ar' => 'المنصب'],
            'tables.hr_employees.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.hr_employees.hire_date' => ['en' => 'Hire Date', 'ar' => 'تاريخ التوظيف'],
            'tables.hr_employees.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_employees.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.hr_employees.filters.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.hr_employees.filters.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.hr_employees.filters.department' => ['en' => 'Department', 'ar' => 'القسم'],
            'tables.hr_employees.filters.position' => ['en' => 'Position', 'ar' => 'المنصب'],

            // Holidays Forms
            'forms.hr_holidays.name.label' => ['en' => 'Holiday Name', 'ar' => 'اسم العطلة'],
            'forms.hr_holidays.start_date.label' => ['en' => 'Start Date', 'ar' => 'تاريخ بداية العطلة'],
            'forms.hr_holidays.days_count.label' => ['en' => 'Days Count', 'ar' => 'عدد الأيام'],
            'forms.hr_holidays.days_count.helper' => ['en' => 'Number of days for this holiday', 'ar' => 'عدد الأيام (>=1)'],
            'forms.hr_holidays.description.label' => ['en' => 'Description', 'ar' => 'الوصف'],

            // Holidays Tables
            'tables.hr_holidays.name' => ['en' => 'Holiday Name', 'ar' => 'اسم العطلة'],
            'tables.hr_holidays.start_date' => ['en' => 'Start Date', 'ar' => 'تاريخ البداية'],
            'tables.hr_holidays.end_date' => ['en' => 'End Date', 'ar' => 'تاريخ النهاية'],
            'tables.hr_holidays.days_count' => ['en' => 'Days', 'ar' => 'الأيام'],
            'tables.hr_holidays.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'tables.hr_holidays.created_by' => ['en' => 'Created By', 'ar' => 'تم الإنشاء بواسطة'],
            'tables.hr_holidays.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.hr_holidays.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
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

        $this->command->info("✓ HR translations created: {$created} entries");
    }
}

