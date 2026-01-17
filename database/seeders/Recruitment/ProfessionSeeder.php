<?php

namespace Database\Seeders\Recruitment;

use App\Models\Recruitment\Profession;
use Illuminate\Database\Seeder;

class ProfessionSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Professions...');

        $professions = [
            ['name_ar' => 'مهندس', 'name_en' => 'Engineer', 'code' => 'ENG', 'is_active' => true],
            ['name_ar' => 'طبيب', 'name_en' => 'Doctor', 'code' => 'DOC', 'is_active' => true],
            ['name_ar' => 'ممرض', 'name_en' => 'Nurse', 'code' => 'NUR', 'is_active' => true],
            ['name_ar' => 'معلم', 'name_en' => 'Teacher', 'code' => 'TCH', 'is_active' => true],
            ['name_ar' => 'محاسب', 'name_en' => 'Accountant', 'code' => 'ACC', 'is_active' => true],
            ['name_ar' => 'محامي', 'name_en' => 'Lawyer', 'code' => 'LAW', 'is_active' => true],
            ['name_ar' => 'سائق', 'name_en' => 'Driver', 'code' => 'DRV', 'is_active' => true],
            ['name_ar' => 'طباخ', 'name_en' => 'Cook', 'code' => 'COK', 'is_active' => true],
            ['name_ar' => 'خادم منزل', 'name_en' => 'Housekeeper', 'code' => 'HSK', 'is_active' => true],
            ['name_ar' => 'حارس', 'name_en' => 'Security Guard', 'code' => 'SEC', 'is_active' => true],
            ['name_ar' => 'نجار', 'name_en' => 'Carpenter', 'code' => 'CAR', 'is_active' => true],
            ['name_ar' => 'سباك', 'name_en' => 'Plumber', 'code' => 'PLU', 'is_active' => true],
            ['name_ar' => 'كهربائي', 'name_en' => 'Electrician', 'code' => 'ELE', 'is_active' => true],
            ['name_ar' => 'ميكانيكي', 'name_en' => 'Mechanic', 'code' => 'MEC', 'is_active' => true],
            ['name_ar' => 'حداد', 'name_en' => 'Blacksmith', 'code' => 'BLK', 'is_active' => true],
            ['name_ar' => 'بناء', 'name_en' => 'Builder', 'code' => 'BLD', 'is_active' => true],
            ['name_ar' => 'رسام', 'name_en' => 'Painter', 'code' => 'PNT', 'is_active' => true],
            ['name_ar' => 'مزارع', 'name_en' => 'Farmer', 'code' => 'FRM', 'is_active' => true],
            ['name_ar' => 'عامل', 'name_en' => 'Worker', 'code' => 'WRK', 'is_active' => true],
            ['name_ar' => 'مشرف', 'name_en' => 'Supervisor', 'code' => 'SUP', 'is_active' => true],
            ['name_ar' => 'مدير', 'name_en' => 'Manager', 'code' => 'MGR', 'is_active' => true],
            ['name_ar' => 'سكرتير', 'name_en' => 'Secretary', 'code' => 'SEC', 'is_active' => true],
            ['name_ar' => 'موظف استقبال', 'name_en' => 'Receptionist', 'code' => 'REC', 'is_active' => true],
            ['name_ar' => 'بائع', 'name_en' => 'Salesperson', 'code' => 'SAL', 'is_active' => true],
            ['name_ar' => 'منسق', 'name_en' => 'Coordinator', 'code' => 'COR', 'is_active' => true],
        ];

        $created = 0;
        foreach ($professions as $profession) {
            Profession::updateOrCreate(
                ['code' => $profession['code']],
                $profession
            );
            $created++;
        }

        $this->command->info("✓ Professions seeded: {$created}");
    }
}
