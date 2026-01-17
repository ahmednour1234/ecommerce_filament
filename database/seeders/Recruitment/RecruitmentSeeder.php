<?php

namespace Database\Seeders\Recruitment;

use Illuminate\Database\Seeder;

class RecruitmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->info('Seeding Recruitment Module...');
        $this->command->info('═══════════════════════════════════════════════════════');
        $this->command->newLine();

        $this->call([
            RecruitmentPermissionsSeeder::class,
            RecruitmentTranslationsSeeder::class,
            NationalitySeeder::class,
            ProfessionSeeder::class,
            AgentSeeder::class,
            LaborerSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✓ Recruitment module seeding completed');
    }
}
