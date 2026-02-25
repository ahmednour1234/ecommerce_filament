<?php

namespace App\Services\Housing;

use App\Models\Housing\HousingRequest;
use App\Models\Housing\HousingAssignment;
use Illuminate\Support\Facades\DB;

class HousingDashboardStatsService
{
    public function getRequestStats(array $filters = []): array
    {
        $query = HousingRequest::query();

        if (isset($filters['request_type'])) {
            $query->where('request_type', $filters['request_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('request_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('request_date', '<=', $filters['to_date']);
        }

        $stats = [
            'completed' => 0,
            'approved' => 0,
            'pending' => 0,
            'rejected' => 0,
            'suspended' => 0,
            'total' => 0,
        ];

        $results = $query->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        foreach ($results as $result) {
            $status = $result->status;
            $count = (int) $result->count;

            if (isset($stats[$status])) {
                $stats[$status] = $count;
            }
            $stats['total'] += $count;
        }

        return $stats;
    }

    public function getWorkerCountsByStatus(): array
    {
        $assignments = HousingAssignment::query()
            ->whereNull('end_date')
            ->with('status')
            ->get();

        $counts = [
            'total' => $assignments->count(),
            'stopped' => 0,
            'on_leave' => 0,
            'outside_service' => 0,
            'transfer_kafala' => 0,
            'rented' => 0,
        ];

        foreach ($assignments as $assignment) {
            if ($assignment->status) {
                $statusKey = $assignment->status->key;
                if (isset($counts[$statusKey])) {
                    $counts[$statusKey]++;
                }
            }
        }

        return $counts;
    }
}
