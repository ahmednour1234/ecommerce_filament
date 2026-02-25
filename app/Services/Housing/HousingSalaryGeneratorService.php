<?php

namespace App\Services\Housing;

use App\Models\Housing\HousingSalaryBatch;
use App\Models\Housing\HousingSalaryItem;
use App\Models\Housing\HousingAssignment;
use App\Models\Housing\HousingSalaryDeduction;
use App\Models\Recruitment\Laborer;
use Illuminate\Support\Facades\DB;

class HousingSalaryGeneratorService
{
    public function generateBatchForMonth(string $month): HousingSalaryBatch
    {
        if (HousingSalaryBatch::where('month', $month)->exists()) {
            throw new \Exception("Salary batch for {$month} already exists");
        }

        $batch = HousingSalaryBatch::create([
            'month' => $month,
            'total_salaries' => 0,
            'total_paid' => 0,
            'total_pending' => 0,
            'total_deductions' => 0,
        ]);

        $activeAssignments = HousingAssignment::whereNull('end_date')
            ->with('laborer')
            ->get();

        $totalSalaries = 0;
        $totalDeductions = 0;

        foreach ($activeAssignments as $assignment) {
            $laborer = $assignment->laborer;
            $basicSalary = $laborer->monthly_salary_amount ?? 0;

            $deductions = HousingSalaryDeduction::where('laborer_id', $laborer->id)
                ->where('status', 'applied')
                ->whereMonth('deduction_date', substr($month, 5, 2))
                ->whereYear('deduction_date', substr($month, 0, 4))
                ->sum('amount');

            $netSalary = max(0, $basicSalary - $deductions);

            HousingSalaryItem::create([
                'batch_id' => $batch->id,
                'laborer_id' => $laborer->id,
                'basic_salary' => $basicSalary,
                'deductions_total' => $deductions,
                'net_salary' => $netSalary,
                'status' => 'pending',
            ]);

            $totalSalaries += $basicSalary;
            $totalDeductions += $deductions;
        }

        $batch->update([
            'total_salaries' => $totalSalaries,
            'total_pending' => $totalSalaries - $totalDeductions,
            'total_deductions' => $totalDeductions,
        ]);

        return $batch->fresh();
    }

    public function updateBatchTotals(HousingSalaryBatch $batch): void
    {
        $items = $batch->items;

        $batch->update([
            'total_salaries' => $items->sum('basic_salary'),
            'total_paid' => $items->where('status', 'paid')->sum('net_salary'),
            'total_pending' => $items->where('status', 'pending')->sum('net_salary'),
            'total_deductions' => $items->sum('deductions_total'),
        ]);
    }
}
