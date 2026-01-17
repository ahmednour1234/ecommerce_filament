<?php

namespace App\Services\HR;

use App\Models\HR\EmployeeFinancialProfile;
use App\Models\HR\LoanInstallment;
use Carbon\Carbon;

class PayrollCalculationService
{
    public function calculateSalary(EmployeeFinancialProfile $profile, bool $includeAttendanceDeductions, bool $includeLoanInstallments, int $year, int $month): array
    {
        $earnings = [];
        $deductions = [];

        foreach ($profile->salaryItems as $salaryItem) {
            $component = $salaryItem->component;
            $amount = $salaryItem->amount;

            if ($component->type === 'earning') {
                $earnings[$component->id] = ($earnings[$component->id] ?? 0) + $amount;
            } else {
                $deductions[$component->id] = ($deductions[$component->id] ?? 0) + $amount;
            }
        }

        if ($includeLoanInstallments) {
            $loanDeductions = $this->calculateLoanInstallments($profile->employee_id, $year, $month);
            foreach ($loanDeductions as $componentId => $amount) {
                $deductions[$componentId] = ($deductions[$componentId] ?? 0) + $amount;
            }
        }

        if ($includeAttendanceDeductions) {
            $attendanceDeductions = $this->calculateAttendanceDeductions($profile->employee_id, $year, $month);
            foreach ($attendanceDeductions as $componentId => $amount) {
                $deductions[$componentId] = ($deductions[$componentId] ?? 0) + $amount;
            }
        }

        $totalEarnings = array_sum($earnings);
        $totalDeductions = array_sum($deductions);
        $netSalary = $profile->base_salary + $totalEarnings - $totalDeductions;

        return [
            'earnings' => $earnings,
            'deductions' => $deductions,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_salary' => max(0, $netSalary),
        ];
    }

    protected function calculateLoanInstallments(int $employeeId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $installments = LoanInstallment::whereHas('loan', function ($query) use ($employeeId) {
            $query->where('employee_id', $employeeId)
                ->where('status', 'active');
        })
            ->where('status', 'pending')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->get();

        $deductions = [];

        $loanComponent = \App\Models\HR\SalaryComponent::where('code', 'loan_installment')->first();
        if ($loanComponent) {
            $totalAmount = $installments->sum('amount');
            if ($totalAmount > 0) {
                $deductions[$loanComponent->id] = $totalAmount;
            }
        }

        return $deductions;
    }

    protected function calculateAttendanceDeductions(int $employeeId, int $year, int $month): array
    {
        $deductions = [];

        return $deductions;
    }
}
