<?php

namespace App\Services\HR;

use App\Models\HR\Employee;
use App\Models\HR\EmployeeCommissionAssignment;
use App\Models\Recruitment\RecruitmentContract;
use Illuminate\Support\Collection;

class EmployeeCommissionCalculator
{
    protected array $completedStatuses = [
        'worker_received',
        'closed',
        'arrived_in_saudi_arabia',
    ];

    public function __construct(array $completedStatuses = null)
    {
        if ($completedStatuses !== null) {
            $this->completedStatuses = $completedStatuses;
        }
    }

    public function calculate(int $employeeId, string $dateFrom, string $dateTo): array
    {
        $employee = Employee::findOrFail($employeeId);

        $assignments = EmployeeCommissionAssignment::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->with(['commission.commissionType', 'commission.tiers' => function ($query) {
                $query->where('is_active', true)->orderBy('contracts_from');
            }])
            ->get();

        $results = [];
        $totalContracts = 0;
        $totalCommission = 0;

        foreach ($assignments as $assignment) {
            $commission = $assignment->commission;

            $contracts = $this->getContractsForCommission($employeeId, $commission, $dateFrom, $dateTo);
            $contractCount = $contracts->count();

            if ($contractCount === 0) {
                continue;
            }

            $tier = $this->findMatchingTier($commission, $contractCount);

            if (!$tier) {
                continue;
            }

            $commissionAmount = $contractCount * $tier->amount_per_contract;

            $results[] = [
                'commission_id' => $commission->id,
                'commission_name' => $commission->name_ar,
                'commission_type' => $commission->commissionType->name_ar ?? '',
                'contract_count' => $contractCount,
                'tier_from' => $tier->contracts_from,
                'tier_to' => $tier->contracts_to,
                'amount_per_contract' => $tier->amount_per_contract,
                'total' => $commissionAmount,
            ];

            $totalContracts += $contractCount;
            $totalCommission += $commissionAmount;
        }

        return [
            'employee' => $employee,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'results' => $results,
            'total_contracts' => $totalContracts,
            'total_commission' => $totalCommission,
        ];
    }

    protected function getContractsForCommission(int $employeeId, $commission, string $dateFrom, string $dateTo): Collection
    {
        $query = RecruitmentContract::where('marketer_id', $employeeId)
            ->whereBetween('gregorian_request_date', [$dateFrom, $dateTo])
            ->whereIn('status', $this->completedStatuses);

        return $query->get();
    }

    protected function findMatchingTier($commission, int $contractCount)
    {
        $tiers = $commission->tiers->where('is_active', true);

        foreach ($tiers as $tier) {
            if ($tier->matchesContractCount($contractCount)) {
                return $tier;
            }
        }

        return null;
    }

    public function setCompletedStatuses(array $statuses): self
    {
        $this->completedStatuses = $statuses;
        return $this;
    }

    public function getCompletedStatuses(): array
    {
        return $this->completedStatuses;
    }
}
