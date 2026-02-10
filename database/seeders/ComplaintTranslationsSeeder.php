<?php

namespace Database\Seeders;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Database\Seeder;

class ComplaintTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->warn('English or Arabic language not found. Skipping Complaint translations.');
            return;
        }

        $this->command->info('Creating Complaint translations...');

        $translations = [
            'sidebar.complaints.complaints' => ['en' => 'Complaints', 'ar' => 'قسم الشكاوي'],

            'complaint.sections.basic_info' => ['en' => 'Basic Information', 'ar' => 'المعلومات الأساسية'],
            'complaint.sections.assignment' => ['en' => 'Assignment', 'ar' => 'التعيين'],
            'complaint.sections.resolution' => ['en' => 'Resolution', 'ar' => 'الحل'],

            'complaint.fields.complaint_no' => ['en' => 'Complaint No', 'ar' => 'رقم الشكوى'],
            'complaint.fields.contract_type' => ['en' => 'Contract Type', 'ar' => 'نوع العقد'],
            'complaint.fields.contract' => ['en' => 'Contract', 'ar' => 'العقد'],
            'complaint.fields.subject' => ['en' => 'Subject', 'ar' => 'الموضوع'],
            'complaint.fields.description' => ['en' => 'Description', 'ar' => 'الوصف'],
            'complaint.fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'complaint.fields.priority' => ['en' => 'Priority', 'ar' => 'الأولوية'],
            'complaint.fields.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'complaint.fields.assigned_to' => ['en' => 'Assigned To', 'ar' => 'مكلف به'],
            'complaint.fields.resolution_notes' => ['en' => 'Resolution Notes', 'ar' => 'ملاحظات الحل'],
            'complaint.fields.resolved_at' => ['en' => 'Resolved At', 'ar' => 'تم الحل في'],

            'complaint.contract_type.rental' => ['en' => 'Rental Contract', 'ar' => 'عقد الإيجار'],
            'complaint.contract_type.recruitment' => ['en' => 'Recruitment Contract', 'ar' => 'عقد الاستقدام'],

            'complaint.status.pending' => ['en' => 'Pending', 'ar' => 'قيد الانتظار'],
            'complaint.status.in_progress' => ['en' => 'In Progress', 'ar' => 'قيد المعالجة'],
            'complaint.status.resolved' => ['en' => 'Resolved', 'ar' => 'تم الحل'],
            'complaint.status.closed' => ['en' => 'Closed', 'ar' => 'مغلق'],

            'complaint.priority.low' => ['en' => 'Low', 'ar' => 'منخفض'],
            'complaint.priority.medium' => ['en' => 'Medium', 'ar' => 'متوسط'],
            'complaint.priority.high' => ['en' => 'High', 'ar' => 'عالي'],
            'complaint.priority.urgent' => ['en' => 'Urgent', 'ar' => 'عاجل'],

            'tables.complaints.complaint_no' => ['en' => 'Complaint No', 'ar' => 'رقم الشكوى'],
            'tables.complaints.subject' => ['en' => 'Subject', 'ar' => 'الموضوع'],
            'tables.complaints.contract' => ['en' => 'Contract', 'ar' => 'العقد'],
            'tables.complaints.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.complaints.priority' => ['en' => 'Priority', 'ar' => 'الأولوية'],
            'tables.complaints.assigned_to' => ['en' => 'Assigned To', 'ar' => 'مكلف به'],
            'tables.complaints.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.complaints.created_at' => ['en' => 'Created At', 'ar' => 'تاريخ الإنشاء'],
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

        $this->command->info("✓ Complaint translations created: {$created}, updated: {$updated}");
    }
}
