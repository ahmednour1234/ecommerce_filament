<?php

namespace Database\Seeders\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class TablesTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping tables translations.');
            return;
        }

        $translations = [
            // Common Table Columns
            'tables.common.id' => ['en' => 'ID', 'ar' => 'المعرف'],
            'tables.common.name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'tables.common.code' => ['en' => 'Code', 'ar' => 'الرمز'],
            'tables.common.title' => ['en' => 'Title', 'ar' => 'العنوان'],
            'tables.common.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'tables.common.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.common.is_active' => ['en' => 'Active', 'ar' => 'نشط'],
            'tables.common.created_at' => ['en' => 'Created At', 'ar' => 'تم الإنشاء في'],
            'tables.common.updated_at' => ['en' => 'Updated At', 'ar' => 'تم التحديث في'],
            'tables.common.actions' => ['en' => 'Actions', 'ar' => 'الإجراءات'],

            // Table Filters
            'tables.filters.search' => ['en' => 'Search', 'ar' => 'بحث'],
            'tables.filters.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.filters.date_from' => ['en' => 'From Date', 'ar' => 'من تاريخ'],
            'tables.filters.date_to' => ['en' => 'To Date', 'ar' => 'إلى تاريخ'],
            'tables.filters.active_only' => ['en' => 'Active Only', 'ar' => 'النشطة فقط'],
            'tables.filters.all' => ['en' => 'All', 'ar' => 'الكل'],

            // Table Actions
            'tables.actions.view' => ['en' => 'View', 'ar' => 'عرض'],
            'tables.actions.edit' => ['en' => 'Edit', 'ar' => 'تعديل'],
            'tables.actions.delete' => ['en' => 'Delete', 'ar' => 'حذف'],
            'tables.actions.duplicate' => ['en' => 'Duplicate', 'ar' => 'نسخ'],
            'tables.actions.export' => ['en' => 'Export', 'ar' => 'تصدير'],

            // Empty States
            'tables.empty_state.heading' => ['en' => 'No records found', 'ar' => 'لا توجد سجلات'],
            'tables.empty_state.description' => ['en' => 'Get started by creating a new record', 'ar' => 'ابدأ بإنشاء سجل جديد'],
            'tables.empty_state.create_button' => ['en' => 'Create', 'ar' => 'إنشاء'],

            // Pagination
            'tables.pagination.showing' => ['en' => 'Showing', 'ar' => 'عرض'],
            'tables.pagination.to' => ['en' => 'to', 'ar' => 'إلى'],
            'tables.pagination.of' => ['en' => 'of', 'ar' => 'من'],
            'tables.pagination.results' => ['en' => 'results', 'ar' => 'نتيجة'],
            'tables.pagination.previous' => ['en' => 'Previous', 'ar' => 'السابق'],
            'tables.pagination.next' => ['en' => 'Next', 'ar' => 'التالي'],

            // Summaries
            'tables.summary.total' => ['en' => 'Total', 'ar' => 'الإجمالي'],
            'tables.summary.count' => ['en' => 'Count', 'ar' => 'العدد'],
            'tables.summary.sum' => ['en' => 'Sum', 'ar' => 'المجموع'],
            'tables.summary.average' => ['en' => 'Average', 'ar' => 'المتوسط'],
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

        $this->command->info("Tables translations seeded: {$created} created, {$updated} updated.");
    }
}

