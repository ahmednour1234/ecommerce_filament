<?php

namespace Database\Seeders\HR;

use App\Models\HR\Department;
use App\Models\HR\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding HR positions...');

        $positions = [
            // Add positions data here
            // Example structure:
            // [
            //     'title' => 'Position Title (Arabic)',
            //     'department_slug' => 'department-slug',
            //     'description' => 'Optional description',
            //     'active' => true,
            // ],
        ];

        if (empty($positions)) {
            $this->command->warn('No positions data provided. Skipping positions seeding.');
            return;
        }

        DB::transaction(function () use ($positions) {
            $created = 0;
            foreach ($positions as $positionData) {
                $department = Department::where('slug', $positionData['department_slug'])->first();
                
                if (!$department) {
                    $this->command->warn("Department with slug '{$positionData['department_slug']}' not found. Skipping position '{$positionData['title']}'.");
                    continue;
                }

                Position::updateOrCreate(
                    [
                        'title' => $positionData['title'],
                        'department_id' => $department->id,
                    ],
                    [
                        'description' => $positionData['description'] ?? null,
                        'active' => $positionData['active'] ?? true,
                    ]
                );
                $created++;
            }
            $this->command->info("✓ Positions seeded: {$created}");
        });

        $this->command->info('✓ HR positions seeding completed');
    }
}
