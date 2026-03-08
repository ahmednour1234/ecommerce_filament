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
            'sidebar.complaints.report' => ['en' => 'Complaints Report', 'ar' => 'تقرير الشكاوي'],

            'complaint.sections.basic_info' => ['en' => 'Basic Information', 'ar' => 'المعلومات الأساسية'],
            'complaint.sections.assignment' => ['en' => 'Assignment', 'ar' => 'التعيين'],
            'complaint.sections.resolution' => ['en' => 'Resolution', 'ar' => 'الحل'],

            'complaint.fields.complaint_no' => ['en' => 'Complaint No', 'ar' => 'رقم الشكوى'],
            'complaint.fields.contract_type' => ['en' => 'Contract Type', 'ar' => 'نوع العقد'],
            'complaint.fields.contract' => ['en' => 'Contract', 'ar' => 'العقد'],
            'complaint.fields.complaint_description' => ['en' => 'Complaint Description', 'ar' => 'وصف الشكوي'],
            'complaint.fields.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'complaint.fields.priority' => ['en' => 'Priority', 'ar' => 'الأولوية'],
            'complaint.fields.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'complaint.fields.assigned_to' => ['en' => 'Assigned To', 'ar' => 'مكلف به'],
            'complaint.fields.branch_action_taken' => ['en' => 'Action Taken by Branch', 'ar' => 'الإجراء المتخذ من الفرع المختص'],
            'complaint.fields.resolution_notes' => ['en' => 'Resolution Notes', 'ar' => 'ملاحظات الحل'],
            'complaint.fields.resolved_at' => ['en' => 'Resolved At', 'ar' => 'تم الحل في'],
            'complaint.fields.in_progress_at' => ['en' => 'In Progress At', 'ar' => 'قيد المعالجة في'],
            'complaint.fields.problem_type' => ['en' => 'Problem Type', 'ar' => 'نوع المشكلة'],
            'complaint.fields.phone_number' => ['en' => 'Phone Number', 'ar' => 'رقم التليفون'],
            'complaint.fields.nationality' => ['en' => 'Nationality', 'ar' => 'الجنسية'],
            'complaint.fields.old_status' => ['en' => 'Old Status', 'ar' => 'الحالة السابقة'],
            'complaint.fields.new_status' => ['en' => 'New Status', 'ar' => 'الحالة الجديدة'],
            'complaint.fields.status_date' => ['en' => 'Status Date', 'ar' => 'تاريخ الحالة'],
            'complaint.fields.notes' => ['en' => 'Notes', 'ar' => 'ملاحظات'],
            'complaint.fields.created_by' => ['en' => 'Created By', 'ar' => 'تم الإنشاء بواسطة'],

            'complaint.problem_type.salary_issue' => ['en' => 'Salary Issue', 'ar' => 'مشكلة رواتب'],
            'complaint.problem_type.food_issue' => ['en' => 'Food Issue', 'ar' => 'مشكلة طعام'],
            'complaint.problem_type.escape' => ['en' => 'Escape', 'ar' => 'هروب'],
            'complaint.problem_type.work_refusal' => ['en' => 'Work Refusal', 'ar' => 'رفض عمل'],

            'complaint.contract_type.rental' => ['en' => 'Rental Contract', 'ar' => 'عقد الإيجار'],
            'complaint.contract_type.recruitment' => ['en' => 'Recruitment Contract', 'ar' => 'عقد الاستقدام'],

            'complaint.status.in_progress' => ['en' => 'In Progress', 'ar' => 'قيد المعالجة'],
            'complaint.status.resolved' => ['en' => 'Resolved', 'ar' => 'تم الحل'],

            'complaint.priority.very_high' => ['en' => 'Very High', 'ar' => 'عالي جدا'],

            'complaint.tabs.status_logs' => ['en' => 'Status Logs', 'ar' => 'سجل الحالات'],

            'complaint.report.title' => ['en' => 'Complaints Report', 'ar' => 'تقرير الشكاوي'],
            'complaint.report.heading' => ['en' => 'Complaints Report', 'ar' => 'تقرير الشكاوي'],
            'complaint.report.navigation' => ['en' => 'Complaints Report', 'ar' => 'تقرير الشكاوي'],
            'complaint.report.filters' => ['en' => 'Report Filters', 'ar' => 'فلترة التقرير'],

            'tables.complaints.complaint_no' => ['en' => 'Complaint No', 'ar' => 'رقم الشكوى'],
            'tables.complaints.subject' => ['en' => 'Subject', 'ar' => 'الموضوع'],
            'tables.complaints.contract' => ['en' => 'Contract', 'ar' => 'العقد'],
            'tables.complaints.status' => ['en' => 'Status', 'ar' => 'الحالة'],
            'tables.complaints.priority' => ['en' => 'Priority', 'ar' => 'الأولوية'],
            'tables.complaints.assigned_to' => ['en' => 'Assigned To', 'ar' => 'مكلف به'],
            'tables.complaints.branch' => ['en' => 'Branch', 'ar' => 'الفرع'],
            'tables.complaints.created_at' => ['en' => 'Created At', 'ar' => 'تاريخ الإنشاء'],

            'complaint.dashboard.heading' => ['en' => 'Complaints Statistics', 'ar' => 'إحصائيات الشكاوي'],
            'complaint.dashboard.no_data' => ['en' => 'No Data', 'ar' => 'لا توجد بيانات'],
            'complaint.dashboard.no_complaints_period' => ['en' => 'No complaints in the selected period', 'ar' => 'لا توجد شكاوي في الفترة المحددة'],
            'complaint.dashboard.total_complaints' => ['en' => 'Total Complaints', 'ar' => 'إجمالي الشكاوي'],
            'complaint.dashboard.in_period' => ['en' => 'In the selected period', 'ar' => 'في الفترة المحددة'],
            'complaint.dashboard.pending_complaints' => ['en' => 'Pending Complaints', 'ar' => 'شكاوي قيد الانتظار'],
            'complaint.dashboard.in_progress_complaints' => ['en' => 'In Progress Complaints', 'ar' => 'شكاوي قيد المعالجة'],
            'complaint.dashboard.resolved_complaints' => ['en' => 'Resolved Complaints', 'ar' => 'شكاوي تم حلها'],
            'complaint.dashboard.closed_complaints' => ['en' => 'Closed Complaints', 'ar' => 'شكاوي مغلقة'],
            'complaint.dashboard.urgent_complaints' => ['en' => 'Urgent Complaints', 'ar' => 'شكاوي عاجلة'],
            'complaint.dashboard.high_priority_complaints' => ['en' => 'High Priority Complaints', 'ar' => 'شكاوي عالية الأولوية'],
            'complaint.dashboard.medium_priority_complaints' => ['en' => 'Medium Priority Complaints', 'ar' => 'شكاوي متوسطة الأولوية'],
            'complaint.dashboard.low_priority_complaints' => ['en' => 'Low Priority Complaints', 'ar' => 'شكاوي منخفضة الأولوية'],
            'complaint.dashboard.requires_attention' => ['en' => 'Requires Immediate Attention', 'ar' => 'يحتاج إلى انتباه فوري'],
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
