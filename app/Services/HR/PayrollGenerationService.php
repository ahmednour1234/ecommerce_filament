<?php

namespace App\Services\HR;

use App\Models\HR\Employee;
use App\Models\HR\EmployeeFinancialProfile;
use App\Models\HR\LoanInstallment;
use App\Models\HR\PayrollRun;
use App\Models\HR\PayrollRunItem;
use App\Models\HR\PayrollRunItemLine;
use App\Models\HR\SalaryComponent;
use Illuminate\Support\Facades\DB;

class PayrollGenerationService
{
    protected PayrollCalculationService $calculationService;

    public function __construct(PayrollCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    public function generatePayroll(int $year, int $month, ?int $departmentId, bool $includeAttendanceDeductions, bool $includeLoanInstallments, ?int $generatedBy = null): PayrollRun
    {
        return DB::transaction(function () use ($year, $month, $departmentId, $includeAttendanceDeductions, $includeLoanInstallments, $generatedBy) {
            $payrollRun = PayrollRun::create([
                'year' => $year,
                'month' => $month,
                'department_id' => $departmentId,
                'include_attendance_deductions' => $includeAttendanceDeductions,
                'include_loan_installments' => $includeLoanInstallments,
                'status' => 'draft',
                'generated_by' => $generatedBy ?? auth()->id(),
                'generated_at' => now(),
            ]);

            $employees = $this->getEmployeesForPayroll($departmentId);

            foreach ($employees as $employee) {
                $this->generatePayrollItem($payrollRun, $employee, $includeAttendanceDeductions, $includeLoanInstallments);
            }

            return $payrollRun->fresh();
        });
    }

    protected function getEmployeesForPayroll(?int $departmentId)
    {
        $query = Employee::where('status', 'active')
            ->with(['financialProfile.salaryItems.component', 'department']);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        return $query->get();
    }

    protected function generatePayrollItem(PayrollRun $payrollRun, Employee $employee, bool $includeAttendanceDeductions, bool $includeLoanInstallments): PayrollRunItem
    {
        $profile = $employee->financialProfile;

        if (!$profile) {
            $profile = EmployeeFinancialProfile::create([
                'employee_id' => $employee->id,
                'base_salary' => $employee->basic_salary ?? 0,
                'status' => 'active',
            ]);
        }

        $basicSalary = $profile->base_salary;

        $calculations = $this->calculationService->calculateSalary(
            $profile,
            $includeAttendanceDeductions,
            $includeLoanInstallments,
            $payrollRun->year,
            $payrollRun->month
        );

        $payrollItem = PayrollRunItem::create([
            'payroll_run_id' => $payrollRun->id,
            'employee_id' => $employee->id,
            'basic_salary' => $basicSalary,
            'total_earnings' => $calculations['total_earnings'],
            'total_deductions' => $calculations['total_deductions'],
            'net_salary' => $calculations['net_salary'],
            'status' => 'pending',
        ]);

        foreach ($calculations['earnings'] as $componentId => $amount) {
            PayrollRunItemLine::create([
                'payroll_run_item_id' => $payrollItem->id,
                'component_id' => $componentId,
                'type' => 'earning',
                'amount' => $amount,
            ]);
        }

        foreach ($calculations['deductions'] as $componentId => $amount) {
            PayrollRunItemLine::create([
                'payroll_run_item_id' => $payrollItem->id,
                'component_id' => $componentId,
                'type' => 'deduction',
                'amount' => $amount,
            ]);
        }

        return $payrollItem;
    }

    public function approveAll(PayrollRun $payrollRun): void
    {
        DB::transaction(function () use ($payrollRun) {
            $payrollRun->update(['status' => 'approved']);
            $payrollRun->items()->update(['status' => 'pending']);
        });
    }

    public function payAll(PayrollRun $payrollRun): void
    {
        DB::transaction(function () use ($payrollRun) {
            $payrollRun->update(['status' => 'paid']);
            $payrollRun->items()->update(['status' => 'paid']);
        });
    }
}
