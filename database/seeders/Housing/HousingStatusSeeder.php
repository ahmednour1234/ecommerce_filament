<?php

namespace Database\Seeders\Housing;

use App\Models\Housing\HousingStatus;
use Illuminate\Database\Seeder;

class HousingStatusSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating Housing Statuses...');

        $statuses = [
            [
                'key' => 'unpaid_salary',
                'name_ar' => 'عدم دفع الراتب',
                'name_en' => 'Unpaid Salary',
                'color' => 'danger',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'issue',
                'name_ar' => 'مشكلة',
                'name_en' => 'Issue',
                'color' => 'warning',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'key' => 'transfer_sponsorship',
                'name_ar' => 'نقل كفاله',
                'name_en' => 'Sponsorship Transfer',
                'color' => 'info',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'key' => 'work_refused',
                'name_ar' => 'رفض العمل',
                'name_en' => 'Work Refused',
                'color' => 'danger',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'key' => 'runaway',
                'name_ar' => 'هروب',
                'name_en' => 'Runaway',
                'color' => 'danger',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'key' => 'dispute',
                'name_ar' => 'نزاع',
                'name_en' => 'Dispute',
                'color' => 'warning',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'key' => 'ready_for_delivery',
                'name_ar' => 'جاهز للتسليم',
                'name_en' => 'Ready for Delivery',
                'color' => 'success',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'key' => 'with_client',
                'name_ar' => 'مع العميل',
                'name_en' => 'With Client',
                'color' => 'info',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'key' => 'in_completion',
                'name_ar' => 'في الإيماء',
                'name_en' => 'In Completion',
                'color' => 'primary',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'key' => 'completed',
                'name_ar' => 'مكتمل',
                'name_en' => 'Completed',
                'color' => 'success',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'key' => 'outside_warranty',
                'name_ar' => 'خارج الضمان',
                'name_en' => 'Outside Warranty',
                'color' => 'warning',
                'order' => 11,
                'is_active' => true,
            ],
            [
                'key' => 'inside_warranty',
                'name_ar' => 'داخل الضمان',
                'name_en' => 'Inside Warranty',
                'color' => 'success',
                'order' => 12,
                'is_active' => true,
            ],
        ];

        $created = 0;
        foreach ($statuses as $status) {
            $result = HousingStatus::updateOrCreate(
                ['key' => $status['key']],
                $status
            );
            if ($result->wasRecentlyCreated) {
                $created++;
            }
        }

        $this->command->info("✓ Housing statuses created/updated: {$created} new, " . (count($statuses) - $created) . " existing");
    }
}
