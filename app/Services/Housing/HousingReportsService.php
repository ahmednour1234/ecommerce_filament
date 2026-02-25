<?php

namespace App\Services\Housing;

use App\Models\Housing\HousingAssignment;
use App\Models\Recruitment\Laborer;
use Illuminate\Support\Facades\DB;

class HousingReportsService
{
    public function getContractReport(array $filters = []): array
    {
        $query = HousingAssignment::query()
            ->with(['laborer', 'building', 'status']);

        if (isset($filters['branch_id'])) {
            $query->whereHas('building', function ($q) use ($filters) {
                $q->where('branch_id', $filters['branch_id']);
            });
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('start_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('start_date', '<=', $filters['to_date']);
        }

        $assignments = $query->get();

        $stats = [
            'total_workers' => $assignments->pluck('laborer_id')->unique()->count(),
            'total_contracts' => $assignments->count(),
            'total_amount' => $assignments->sum('rent_amount'),
            'total_work_days' => 0,
        ];

        foreach ($assignments as $assignment) {
            $start = $assignment->start_date;
            $end = $assignment->end_date ?? now();
            $days = $start->diffInDays($end) + 1;
            $stats['total_work_days'] += $days;
        }

        $reportData = [];
        $grouped = $assignments->groupBy('laborer_id');

        foreach ($grouped as $laborerId => $laborerAssignments) {
            $laborer = $laborerAssignments->first()->laborer;
            $activeContracts = $laborerAssignments->whereNull('end_date')->count();
            $completedContracts = $laborerAssignments->whereNotNull('end_date')->count();

            $reportData[] = [
                'laborer_id' => $laborerId,
                'laborer_name' => $laborer->name_ar,
                'passport_number' => $laborer->passport_number,
                'phone' => $laborer->phone_1,
                'branch' => $laborerAssignments->first()->building?->branch?->name ?? '-',
                'contracts_count' => $laborerAssignments->count(),
                'active_contracts' => $activeContracts,
                'completed_contracts' => $completedContracts,
                'total_amount' => $laborerAssignments->sum('rent_amount'),
                'total_days' => $laborerAssignments->sum(function ($a) {
                    $start = $a->start_date;
                    $end = $a->end_date ?? now();
                    return $start->diffInDays($end) + 1;
                }),
            ];
        }

        return [
            'stats' => $stats,
            'data' => $reportData,
        ];
    }
}
