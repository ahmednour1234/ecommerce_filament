<?php

namespace Database\Seeders\HR;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class EmployeeCommissionTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping employee commission translations.');
            return;
        }

        $translations = [
            // Menu items
            'sidebar.hr.commissions' => ['en' => 'Employee Commissions', 'ar' => 'عمولات الموظفين'],
            'sidebar.hr.commission_types' => ['en' => 'Commission Types', 'ar' => 'أنواع العمولات'],
            'sidebar.hr.commission_tiers' => ['en' => 'Commission Tiers', 'ar' => 'شرائح عمولات الموظفين'],
            'sidebar.hr.commission_assignments' => ['en' => 'Commission Assignments', 'ar' => 'تعيينات العمولات'],
            'sidebar.hr.commission_report' => ['en' => 'Commission Calculator', 'ar' => 'حساب عمولات الموظفين'],

            // Commission Type - Table columns
            'tables.hr_commission_types.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'tables.hr_commission_types.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'tables.hr_commission_types.is_active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.hr_commission_types.created_at' => ['en' => 'Created At', 'ar' => 'تاريخ الإنشاء'],

            // Commission - Table columns
            'tables.hr_commissions.name_ar' => ['en' => 'Commission Name', 'ar' => 'اسم العمولة'],
            'tables.hr_commissions.commission_type' => ['en' => 'Type', 'ar' => 'نوع العمولة'],
            'tables.hr_commissions.value' => ['en' => 'Value', 'ar' => 'قيمة العمولة'],
            'tables.hr_commissions.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Commission Tier - Table columns
            'tables.hr_employee_commission_tiers.commission' => ['en' => 'Commission', 'ar' => 'العمولة'],
            'tables.hr_employee_commission_tiers.contracts_from' => ['en' => 'From', 'ar' => 'عدد العقود من'],
            'tables.hr_employee_commission_tiers.contracts_to' => ['en' => 'To', 'ar' => 'عدد العقود إلى'],
            'tables.hr_employee_commission_tiers.amount_per_contract' => ['en' => 'Amount Per Contract', 'ar' => 'المبلغ لكل عقد'],
            'tables.hr_employee_commission_tiers.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Commission Assignment - Table columns
            'tables.hr_employee_commission_assignments.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'tables.hr_employee_commission_assignments.commission' => ['en' => 'Commission', 'ar' => 'العمولة'],
            'tables.hr_employee_commission_assignments.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Commission Type - Form fields
            'fields.commission_type.name_ar' => ['en' => 'Name (Arabic)', 'ar' => 'الاسم (عربي)'],
            'fields.commission_type.name_en' => ['en' => 'Name (English)', 'ar' => 'الاسم (إنجليزي)'],
            'fields.commission_type.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Commission - Form fields
            'fields.commission.name_ar' => ['en' => 'Commission Name (Arabic)', 'ar' => 'اسم العمولة (عربي)'],
            'fields.commission.name_en' => ['en' => 'Commission Name (English)', 'ar' => 'اسم العمولة (إنجليزي)'],
            'fields.commission.commission_type_id' => ['en' => 'Commission Type', 'ar' => 'نوع العمولة'],
            'fields.commission.value' => ['en' => 'Value', 'ar' => 'قيمة العمولة'],
            'fields.commission.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Commission Tier - Form fields
            'fields.commission_tier.commission_id' => ['en' => 'Commission', 'ar' => 'العمولة'],
            'fields.commission_tier.contracts_from' => ['en' => 'Contracts From', 'ar' => 'عدد العقود من'],
            'fields.commission_tier.contracts_to' => ['en' => 'Contracts To', 'ar' => 'عدد العقود إلى'],
            'fields.commission_tier.amount_per_contract' => ['en' => 'Amount Per Contract', 'ar' => 'المبلغ لكل عقد'],
            'fields.commission_tier.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Commission Assignment - Form fields
            'fields.commission_assignment.employee_id' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'fields.commission_assignment.commission_id' => ['en' => 'Commission', 'ar' => 'العمولة'],
            'fields.commission_assignment.is_active' => ['en' => 'Active', 'ar' => 'نشط'],

            // Actions
            'actions.create_commission_type' => ['en' => 'Create Commission Type', 'ar' => 'إضافة نوع عمولة'],
            'actions.create_commission' => ['en' => 'Create Commission', 'ar' => 'إضافة عمولة'],
            'actions.create_tier' => ['en' => 'Create Tier', 'ar' => 'إضافة شريحة'],
            'actions.create_assignment' => ['en' => 'Create Assignment', 'ar' => 'إضافة تعيين'],
            'actions.export_excel' => ['en' => 'Export to Excel', 'ar' => 'تصدير إلى Excel'],
            'actions.print' => ['en' => 'Print', 'ar' => 'طباعة'],
            'actions.search' => ['en' => 'Search', 'ar' => 'بحث'],

            // Report page
            'pages.hr.commission_report.title' => ['en' => 'Employee Commission Calculator', 'ar' => 'حساب عمولات الموظفين'],
            'pages.hr.commission_report.filters.employee' => ['en' => 'Employee', 'ar' => 'الموظف'],
            'pages.hr.commission_report.filters.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'pages.hr.commission_report.filters.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'pages.hr.commission_report.table.commission' => ['en' => 'Commission', 'ar' => 'العمولة'],
            'pages.hr.commission_report.table.commission_type' => ['en' => 'Type', 'ar' => 'نوع العمولة'],
            'pages.hr.commission_report.table.contract_count' => ['en' => 'Contract Count', 'ar' => 'عدد العقود'],
            'pages.hr.commission_report.table.tier_range' => ['en' => 'Tier Range', 'ar' => 'الشريحة'],
            'pages.hr.commission_report.table.amount_per_contract' => ['en' => 'Amount Per Contract', 'ar' => 'المبلغ لكل عقد'],
            'pages.hr.commission_report.table.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],
            'pages.hr.commission_report.table.grand_total' => ['en' => 'Grand Total', 'ar' => 'الإجمالي الكلي'],
            'pages.hr.commission_report.table.total_contracts' => ['en' => 'Total Contracts', 'ar' => 'إجمالي العقود'],
            'pages.hr.commission_report.no_results' => ['en' => 'No results found', 'ar' => 'لا توجد نتائج'],
        ];

        $created = 0;
        $updated = 0;

        foreach ($translations as $key => $values) {
            // English translation
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

            // Arabic translation
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

        $this->command->info("Employee commission translations seeded: {$created} created, {$updated} updated.");
    }
}
