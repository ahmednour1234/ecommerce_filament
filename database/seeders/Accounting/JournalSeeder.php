<?php

namespace Database\Seeders\Accounting;

use App\Models\Accounting\Journal;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        $journals = [
            [
                'code' => 'GEN',
                'name' => 'General Journal',
                'type' => 'general',
                'is_active' => true,
            ],
            [
                'code' => 'SALES',
                'name' => 'Sales Journal',
                'type' => 'sales',
                'is_active' => true,
            ],
            [
                'code' => 'PURCH',
                'name' => 'Purchase Journal',
                'type' => 'purchase',
                'is_active' => true,
            ],
            [
                'code' => 'CASH',
                'name' => 'Cash Journal',
                'type' => 'cash',
                'is_active' => true,
            ],
        ];

        foreach ($journals as $journal) {
            Journal::updateOrCreate(
                ['code' => $journal['code']],
                $journal
            );
        }
    }
}

