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
                'name_ar' => 'عدم دفع راتب',
                'name_en' => 'Unpaid Salary',
                'color' => 'danger',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'key' => 'transfer_sponsorship',
                'name_ar' => 'نقل كفاله',
                'name_en' => 'Sponsorship Transfer',
                'color' => 'info',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'key' => 'temporary',
                'name_ar' => 'مؤقته',
                'name_en' => 'Temporary',
                'color' => 'warning',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'key' => 'rental',
                'name_ar' => 'ايجار',
                'name_en' => 'Rental',
                'color' => 'info',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'key' => 'work_refused',
                'name_ar' => 'رفض عمل',
                'name_en' => 'Work Refused',
                'color' => 'danger',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'key' => 'runaway',
                'name_ar' => 'هروب',
                'name_en' => 'Runaway',
                'color' => 'danger',
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
                'key' => 'in_accommodation',
                'name_ar' => 'في الايواء',
                'name_en' => 'In Accommodation',
                'color' => 'primary',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'key' => 'outside_kingdom',
                'name_ar' => 'خارج المملكه',
                'name_en' => 'Outside Kingdom',
                'color' => 'warning',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'key' => 'ready_for_travel',
                'name_ar' => 'جاهزه للتسفير',
                'name_en' => 'Ready for Travel',
                'color' => 'success',
                'order' => 11,
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
